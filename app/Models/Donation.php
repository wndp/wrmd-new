<?php

namespace App\Models;

use App\Concerns\QueriesDateRange;
use App\Support\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;
    use QueriesDateRange;

    protected $fillable = [
        'person_id',
        'donated_at',
        'value',
        'method_id',
        'comments',
    ];

    protected $casts = [
        'person_id' => 'integer',
        'donated_at' => 'date',
        'value' => 'integer',
        'method_id' => 'integer',
        'comments' => 'string',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'donated_at_for_humans',
        //'value_for_humans',
        //'donation_for_humans',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    protected function donatedAtForHumans(): Attribute
    {
        return Attribute::get(
            fn () => Timezone::convertToLocal($this->donated_at)->toDayDateTimeString(),
        );
    }
}
