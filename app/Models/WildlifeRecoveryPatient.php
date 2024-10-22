<?php

namespace App\Models;

use App\Concerns\LocksPatient;
use App\Concerns\ValidatesOwnership;
use App\Summarizable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WildlifeRecoveryPatient extends Model implements Summarizable
{
    use HasFactory;
    use HasVersion7Uuids;
    use ValidatesOwnership;
    use LogsActivity;
    use LocksPatient;

    protected $fillable = [
        'surveyid',
        'surveyname',
        'method',
        'spillname',
        'teamname',
        'userorg',
        'surveyuserenter',
        'entrytime',
        'type',
        'count',
        'taxa',
        'subtype',
        'condition',
        'notes',
        'locdesc',
        'highpriority',
        'latitude',
        'longitude',
        'entryacc',
        'entryid',
        'qr_code',
        'photos',
    ];

    protected $casts = [
        'photos' => 'json',
    ];

    protected $appends = [
        'entrytime_at',
        'photo_1_url',
        'photo_2_url',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'patient_id', 'patient_id')
            ->where('admissions.team_id', $this->team_id);
    }

    public function isInHoldingPattern(): bool
    {
        return $this->patient_id === null;
    }

    public function removeFromHoldingPattern(Patient $patient): void
    {
        $this->patient_id = $patient->id;
        $this->save();
    }

    public function originalAttributesToArray(): array
    {
        return collect($this->toArray())
            ->except([
                'id',
                'team_id',
                'patient_id',
                'photo_1_url',
                'photo_2_url',
                'created_at',
                'updated_at'
            ])
            ->transform(function ($value, $key) {
                if (is_array($value)) {
                    return implode(', ', $value);
                }

                return $value;
            })
            // strval hack to convert null values to empty strings
            ->map(fn ($value) => strval($value))
            ->toArray();
    }

    public static function findByHash(string $hash): WildlifeRecoveryPatient
    {
        return self::where('qr_code', "https://wr.md/$hash")->first();
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(
            fn () => 'entrytime_at'
        );
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(function () {
            $exclude = ['id', 'team_id', 'patient_id', 'photos', 'entrytime_at_date', 'entrytime_at_date_time', 'entrytime_at_formatted', 'created_at', 'updated_at'];

            $data = array_diff_key($this->toArray(), array_flip($exclude));

            foreach ($data as $key => $value) {
                $details[] = '<strong>'.ucwords(str_replace('_', ' ', $key)).':</strong> '.$value;
            }

            return implode(', ', $details);
        });
    }

    public function entrytimeAt(): Attribute
    {
        return Attribute::get(
            fn () => Carbon::createFromFormat('Ymd-His', $this->entrytime)
        );
    }

    public function photo1Url(): Attribute
    {
        return Attribute::get(
            fn () => is_array($this->photos) && array_key_exists(0, $this->photos)
                ? Storage::url("wildlife-recovery/".mb_strtoupper($this->spillname)."/photos/{$this->photos[0]}")
                : ''
        );
    }

    public function photo2Url(): Attribute
    {
        $bucket = config('filesystems.disks.s3.bucket');

        return Attribute::get(
            fn () => is_array($this->photos) && array_key_exists(1, $this->photos)
                ? Storage::url("wildlife-recovery/".mb_strtoupper($this->spillname)."/photos/{$this->photos[1]}")
                : ''
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
