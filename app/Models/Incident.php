<?php

namespace App\Models;

use App\Concerns\InteractsWithMedia;
use App\Concerns\QueriesDateRange;
use App\Concerns\QueriesOneOfMany;
use App\Concerns\ValidatesOwnership;
use App\ValueObjects\SingleStorePoint;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;

class Incident extends Model implements HasMedia
{
    use HasFactory;
    use HasVersion7Uuids;
    use InteractsWithMedia;
    use LogsActivity;
    use QueriesDateRange;
    use QueriesOneOfMany;
    use SoftDeletes;
    use ValidatesOwnership;

    protected $fillable = [
        'team_id',
        'responder_id',
        'patient_id',
        'incident_number',
        'reported_at',
        'occurred_at',
        'recorded_by',
        'duration_of_call',
        'category_id',
        'is_priority',
        'suspected_species',
        'number_of_animals',
        'incident_status_id',
        'incident_address',
        'incident_city',
        'incident_subdivision',
        'incident_postal_code',
        'incident_coordinates',
        'description',
        'resolved_at',
        'resolution',
        'given_information',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'team_id' => 'integer',
        'responder_id' => 'integer',
        'patient_id' => 'integer',
        'incident_number' => 'string',
        'reported_at' => 'datetime',
        'occurred_at' => 'datetime',
        'recorded_by' => 'string',
        'duration_of_call' => 'float',
        'category_id' => 'integer',
        'is_priority' => 'boolean',
        'suspected_species' => 'string',
        'number_of_animals' => 'integer',
        'incident_status_id' => 'integer',
        'incident_address' => 'string',
        'incident_city' => 'string',
        'incident_subdivision' => 'string',
        'incident_postal_code' => 'string',
        'incident_coordinates' => SingleStorePoint::class,
        'description' => 'string',
        'resolved_at' => 'datetime',
        'resolution' => 'string',
        'given_information' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function reportingParty(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'responder_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class)->orderByDesc('communication_at')->orderByDesc('created_at');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'incident_status_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'category_id');
    }

    public function addressToGeocode(): Attribute
    {
        return Attribute::get(
            fn () => "$this->incident_address $this->incident_city $this->incident_subdivision $this->incident_postal_code"
        );
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->withTrashed()->findOrFail($value) ?? abort(404);
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($incident) {
            $year = $incident->reported_at?->format('y') ?? date('y');
            $prefix = "HL-$year-";

            $sufix = static::withTrashed()
                ->where('team_id', $incident->team_id)
                ->where('incident_number', 'like', $prefix.'%')
                ->count() + 1;

            $incident->incident_number = $prefix.str_pad($sufix, 4, 0, STR_PAD_LEFT);

            return true;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
