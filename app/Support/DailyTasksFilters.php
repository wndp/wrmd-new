<?php

namespace App\Support;

use App\Enums\DailyTaskSchedulable;
use App\Enums\SettingKey;
use Carbon\Carbon;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class DailyTasksFilters extends Fluent
{
    private $filters = [
        'date',
        'facility',
        'group_by',
        'include',
        'include_non_pending',
        'include_non_possession',
        'slug',
    ];

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->filters)) {
                $key = Str::studly($key);
                $this->{"set{$key}"}($value);
            }
        }
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        if (in_array($key, $this->filters)) {
            $key = Str::studly($key);

            return $this->{"default{$key}"}();
        }

        return value($default);
    }

    public function setDate($value)
    {
        $this->attributes['date'] = Carbon::parse($value, Wrmd::settings(SettingKey::TIMEZONE));
    }

    public function setFacility($value)
    {
        $this->attributes['facility'] = $value;
    }

    public function setGroupBy($value)
    {
        $this->attributes['group_by'] = $value;
    }

    public function setSlug($value)
    {
        $this->attributes['slug'] = $value;
    }

    public function setInclude($value)
    {
        if (is_string($value)) {
            $value = Str::of($value)->explode(',')->toArray();
        }

        $this->attributes['include'] = $value;
    }

    public function setIncludeNonPending($value)
    {
        $this->attributes['include_non_pending'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function setIncludeNonPossession($value)
    {
        $this->attributes['include_non_possession'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function defaultDate()
    {
        return Carbon::now(Wrmd::settings(SettingKey::TIMEZONE));
    }

    public static function defaultFacility()
    {
        return 'anywhere';
    }

    public static function defaultSlug()
    {
        return '';
    }

    public static function defaultGroupBy()
    {
        return 'Area';
    }

    public static function defaultIncludeNonPending()
    {
        return false;
    }

    public static function defaultIncludeNonPossession()
    {
        return false;
    }

    public static function defaultInclude()
    {
        return array_column(DailyTaskSchedulable::cases(), 'value');
    }

    public function toArray()
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'facility' => $this->facility,
            'group_by' => $this->group_by,
            'include_non_pending' => $this->include_non_pending,
            'include_non_possession' => $this->include_non_possession,
            'include' => $this->include,
            'slug' => $this->slug,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __get($key)
    {
        // if (in_array($key, ['include_non_pending']) || Str::contains($key, 'exclude')) {
        //     return filter_var($this->get($key), FILTER_VALIDATE_BOOLEAN);
        // }

        if ($key === 'date') {
            return Carbon::parse($this->get('date'), Wrmd::settings(SettingKey::TIMEZONE));
        }

        if ($this->offsetExists($key)) {
            return $this->get($key);
        }

        // if ($key === 'date') {
        //     return self::defaultDate();
        // }

        if ($key === 'facility') {
            return self::defaultFacility();
        }

        if ($key === 'group_by') {
            return self::defaultGroupBy();
        }

        if ($key === 'include_non_pending') {
            return self::defaultIncludeNonPending();
        }

        if ($key === 'include_non_possession') {
            return self::defaultIncludeNonPossession();
        }

        if ($key === 'include') {
            return self::defaultInclude();
        }
    }
}
