<?php

namespace App\Models;

use App\Summarizable;
use App\Support\Timezone;
use App\Support\Wrmd;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model implements Summarizable
{
    use HasFactory;
    use HasVersion7Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'summary',
        'occurrence',
        'occurrence_at',
        'completed_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'occurrence_at' => 'date',
        'completed_at' => 'datetime',
        'occurrence' => 'int',
    ];

    protected $appends = [
        'occurrence_at_for_humans',
        'completed_at_for_humans',
        'created_at_for_humans',
    ];

    public function task()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function summaryDate(): Attribute
    {
        return Attribute::get(
            fn () => 'completed_at'
        );
    }

    public function summaryBody(): Attribute
    {
        return Attribute::get(
            fn () => 'Did: '.Wrmd::humanize(class_basename($this->task_type))
        );
    }

    protected function occurrenceAtForHumans(): Attribute
    {
        return Attribute::get(
            fn () => $this->occurrence_at->toFormattedDayDateString(),
        );
    }

    protected function completedAtForHumans(): Attribute
    {
        return Attribute::get(
            fn () => Timezone::convertFromUtcToLocal($this->completed_at)?->toDayDateTimeString(),
        );
    }

    protected function createdAtForHumans(): Attribute
    {
        return Attribute::get(
            fn () => Timezone::convertFromUtcToLocal($this->created_at)?->toDayDateTimeString(),
        );
    }
}
