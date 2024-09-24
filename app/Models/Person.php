<?php

namespace App\Models;

use App\Concerns\QueriesDateRange;
use App\Concerns\QueriesOneOfMany;
use App\Concerns\ValidatesOwnership;
use App\Models\Patient;
use App\Models\Team;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;
    use QueriesDateRange;
    use QueriesOneOfMany;
    use HasVersion7Uuids;

    protected $fillable = [
        'team_id',
        'entity_id',
        'organization',
        'first_name',
        'last_name',
        'phone',
        'phone_clean',
        'alternate_phone',
        'alternate_phone_clean',
        'email',
        'country',
        'subdivision',
        'city',
        'address',
        'postal_code',
        'county',
        'notes',
        'no_solicitations',
        'is_volunteer',
        'is_member',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'entity_id' => 'integer',
        'organization' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'phone' => 'string',
        'phone_clean' => 'string',
        'alternate_phone' => 'string',
        'alternate_phone_clean' => 'string',
        'email' => 'string',
        'country' => 'string',
        'subdivision' => 'string',
        'city' => 'string',
        'address' => 'string',
        'postal_code' => 'string',
        'county' => 'string',
        'notes' => 'string',
        'no_solicitations' => 'boolean',
        'is_volunteer' => 'boolean',
        'is_member' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'identifier',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'rescuer_id');
    }

    public function hotline(): HasMany
    {
        return $this->hasMany(Incident::class, 'responder_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'entity_id');
    }

    /**
     * Present the persons full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Present an identifier name for the person.
     */
    public function getIdentifierAttribute(): string
    {
        $parts = array_filter(array_map('trim', [$this->organization, $this->first_name.' '.$this->last_name]));

        return implode(', ', $parts) ?: 'Unidentified';
    }
}
