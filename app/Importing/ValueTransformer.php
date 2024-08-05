<?php

namespace App\Importing;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ValueTransformer
{
    /**
     * WRMD column name.
     *
     * @var string
     */
    protected $key;

    /**
     * Translated value.
     *
     * @var string
     */
    protected $value;

    /**
     * WRMD date attributes.
     *
     * @var \Illuminate\Support\Collection
     */
    protected static $dateAttributes;

    /**
     * WRMD number attributes.
     *
     * @var \Illuminate\Support\Collection
     */
    protected static $numberAttributes;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;

        if (! static::$dateAttributes instanceof Collection) {
            self::setDateAttributes();
        }

        if (! static::$numberAttributes instanceof Collection) {
            self::setNumberAttributes();
        }
    }

    /**
     * Determine if the importers dateAttributes collection contains the provided key.
     */
    public function isDateAttribute(string $key): bool
    {
        return self::$dateAttributes->contains($key);
    }

    /**
     * Determine if the importers numberAttributes collection contains the provided key.
     */
    public function isNumberAttribute(string $key): bool
    {
        return self::$numberAttributes->contains($key);
    }

    /**
     * Trim strings.
     */
    public function trimStrings(): static
    {
        $this->value = is_string($this->value) ? trim($this->value) : $this->value;

        return $this;
    }

    /**
     * Convert empty strings to null.
     */
    public function convertEmptyStringsToNull(): static
    {
        $this->value = is_string($this->value) && $this->value === '' ? null : $this->value;

        return $this;
    }

    /**
     * Transform a date value into a Carbon object.
     */
    public function transformDate(): static
    {
        if (! $this->isDateAttribute($this->key)) {
            return $this;
        }

        if (empty($this->value)) {
            return $this;
        }

        try {
            $this->value = Carbon::instance(Date::excelToDateTimeObject($this->value));
        } catch (\Throwable $e) {
            $this->value = Carbon::createFromFormat('Y-m-d', $this->value);
        }

        return $this;
    }

    /**
     * Transform a date value into a Carbon object.
     */
    public function transformNumber(): static
    {
        if (! $this->isNumberAttribute($this->key)) {
            return $this;
        }

        if (empty($this->value)) {
            return $this;
        }

        if (is_numeric($this->value)) {
            $this->value = round($this->value, 2);
        }

        return $this;
    }

    /**
     * Handle the transformations.
     *
     * @return mixed
     */
    public static function handle(string $key, string $value)
    {
        return (new static($key, $value))
            ->trimStrings()
            ->convertEmptyStringsToNull()
            ->transformDate()
            ->transformNumber()
            ->value;
    }

    /**
     * Set the date attributes.
     */
    public static function setDateAttributes()
    {
        self::$dateAttributes = fields('us')
            ->byType('date')
            ->keys()
            ->values();
    }

    /**
     * Set the number attributes.
     */
    public static function setNumberAttributes()
    {
        self::$numberAttributes = fields('us')
            ->byType('number')
            ->keys()
            ->values();
    }
}
