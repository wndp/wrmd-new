<?php

namespace App\Importing\Contracts;

use App\Enums\Attribute;
use App\Enums\ImportFrequency;
use App\Importing\Declarations;
use App\Importing\ImportValueBinder;
use App\Importing\ValueTransformer;
use App\Models\FailedImport;
use App\Models\Team;
use App\Models\User;
use App\Notifications\ImportHasFailedNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LogicException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\ImportFailed;

abstract class Importer implements ShouldQueue, SkipsOnError, WithChunkReading, WithCustomValueBinder, WithEvents, WithHeadingRow
{
    //use Importable;
    use ImportValueBinder;

    /**
     * WRMD attributes that have fixed values.
     *
     * @var \Illuminate\Support\Collection
     */
    public $translatableAttributes;

    /**
     * @var \App\Domain\Users\User
     */
    protected $user;

    /**
     * @var \App\Domain\Accounts\Account
     */
    protected $team;

    /**
     * Import session declarations set by the user.
     *
     * @var \App\Domain\Importing\Declarations
     */
    protected $declarations;

    /**
     * Constructor.
     */
    public function __construct(User $user, Team $team, Declarations $declarations)
    {
        $this->translatableAttributes = $this->getTranslateableAttributes();
        $this->user = $user;
        $this->team = $team;
        $this->declarations = $declarations;

        ValueTransformer::setDateAttributes();
        ValueTransformer::setNumberAttributes();
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event) {
                $this->user->notify(new ImportHasFailedNotification);
            },
        ];
    }

    /**
     * Read the spreadsheet in chunks.
     */
    public function chunkSize(): int
    {
        return ImportFrequency::RECORDS_PER_CHUNK->value;
    }

    /**
     * In case the rows are inserted in batches.
     */
    public function batchSize(): int
    {
        return ImportFrequency::BATCHES_PER_CHUNK->value;
    }

    public function onError(\Throwable $e)
    {
        $this->logFailedImport(new Collection, $e);
    }

    /**
     * Get the translatable attributes.
     */
    public function getTranslateableAttributes(): Collection
    {
        return Collection::make(Attribute::cases())->filter(
            fn ($attribute) => $attribute->hasAttributeOptions()
        )
            ->pluck('value');
    }

    /**
     * Determine if a column has been mapped.
     *
     * @param  string  $wrmdColumn
     * @return bool
     */
    public function isMapped($wrmdColumn)
    {
        return array_key_exists($wrmdColumn, $this->declarations->mappedHeadings);
    }

    /**
     * Get the mapped spreadsheet column name for a wrmd field.
     *
     * @param  string  $wrmdColumn
     * @return string
     */
    public function getMappedColumnFor($wrmdColumn)
    {
        return $this->isMapped($wrmdColumn) ? $this->declarations->mappedHeadings[$wrmdColumn] : null;
    }

    /**
     * Filter the mapped attributes for the provided model.
     *
     * @param  string  $model
     * @return \Illuminate\Support\Collection
     */
    public function filterMappedAttributesForModel($model)
    {
        return collect($this->declarations->mappedHeadings)
            ->filter(function ($importColumn, $wrmdColumn) use ($model) {
                return Str::startsWith($wrmdColumn, $model);
            });
    }

    /**
     * Compose the value to be set on a model attribute.
     *
     * @param  string  $wrmdColumn
     * @param  string|\Illuminate\Support\Collection  $importColumn
     * @return mixed
     */
    public function composeValue($wrmdColumn, $importColumn, ?Collection $row = null)
    {
        if ($importColumn instanceof Collection) {
            $row = $importColumn;
            $importColumn = $this->getMappedColumnFor($wrmdColumn);
        }

        $translated = $this->getTranslation($wrmdColumn, $row->get($importColumn));

        return ValueTransformer::handle($wrmdColumn, $translated);
    }

    /**
     * Get the translation value for the provided column and untranslated value
     * or return the untranslated value if a translated value does not exist.
     *
     * @param  string  $wrmdColumn
     * @param  string  $untranslatedValue
     */
    public function getTranslation($wrmdColumn, $untranslatedValue): ?string
    {
        if ($this->isTranslateable($wrmdColumn)) {
            if ($this->hasTranslation($wrmdColumn, $untranslatedValue)) {
                return $this->declarations->translatedValues[$wrmdColumn][$untranslatedValue];
            }

            return null;
        }

        return $untranslatedValue;
    }

    /**
     * Determine if the provided attribute is a translatable field.
     *
     * @param  string  $attribute
     */
    public function isTranslateable($attribute): bool
    {
        return $this->translatableAttributes->contains($attribute);
    }

    /**
     * Determine if a translated value is set for the provided attribute.
     *
     * @param  string  $attribute
     * @param  string  $untranslatedValue
     */
    public function hasTranslation($attribute, $untranslatedValue): bool
    {
        return isset(
            $this->declarations->translatedValues[$attribute],
            $this->declarations->translatedValues[$attribute][$untranslatedValue]
        );
    }

    /**
     * Find a patient by the spreadsheet identifier column value or throw an exception.
     */
    public function findPatientOrFail(Collection $row): Patient
    {
        $foreignValue = $this->composeValue($this->declarations->wrmdExistingColumn, $this->declarations->spreadsheetExistingColumn, $row);

        $query = Admission::owner($this->team->id);

        switch ($this->declarations->wrmdExistingColumn) {
            case 'case_number':
                [$year, $id] = explode('-', $foreignValue);

                $query->where([
                    'case_year' => Carbon::createFromFormat('y', $year)->format('Y'),
                    'id' => $id,
                ]);
                break;

            case 'reference_number':
                $query->whereHas('patient', function ($query) use ($foreignValue) {
                    $query->where('patients.reference_number', $foreignValue);
                });
                break;

            case 'microchip_number':
                $query->whereHas('patient', function ($query) use ($foreignValue) {
                    $query->where('patients.microchip_number', $foreignValue);
                });
                break;

            case 'band':
                $query->whereHas('patient', function ($query) use ($foreignValue) {
                    $query->where('patients.band', $foreignValue);
                });
                break;

            default:
                throw new LogicException("No query results for patient using wrmdExistingColumn [{$this->declarations->wrmdExistingColumn}] and spreadsheetExistingColumn [{$this->declarations->spreadsheetExistingColumn}]");
                break;
        }

        return $query->firstOrFail()->patient;
    }

    /**
     * Log the failed import to storage.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function logFailedImport(Collection $row, $exception)
    {
        FailedImport::create([
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'session_id' => $this->declarations->sessionId,
            'disclosures' => $this->declarations,
            'row' => $row,
            'exception' => $exception,
        ]);
    }
}
