<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use App\Repositories\AdministrativeDivision;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Veterinarian extends Model
{
    use HasFactory;
    use HasVersion7Uuids;
    use LogsActivity;
    use ValidatesOwnership;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'license',
        'business_name',
        'address',
        'city',
        'subdivision',
        'postal_code',
        'phone',
        'email',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function phone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => app(AdministrativeDivision::class)->phoneNumber($value),
            set: fn ($value) => preg_replace('/[^0-9]/', '', $value)
        );
    }

    protected function formattedInlineAddress(): Attribute
    {
        return Attribute::get(fn () => app(AdministrativeDivision::class)->inlineAddress(
            organization: $this->business_name,
            subdivision: $this->subdivision,
            locality: $this->city,
            postalCode: $this->postal_code,
            addressLine1: $this->address,
        )
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
