<?php

namespace App\Models;

use App\Concerns\ValidatesOwnership;
use App\Enums\PhoneFormat;
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
        'phone_e164',
        'phone_normalized',
        'phone_national',
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

    protected static function booted(): void
    {
        static::saving(function (Veterinarian $veterinarian) {
            $administrativeDivision = app(AdministrativeDivision::class);

            if ($veterinarian->wasChanged('phone') || ! $veterinarian->exists) {
                $veterinarian->phone_normalized = $administrativeDivision->phoneNumber($veterinarian->phone, format: PhoneFormat::NORMALIZED);
                $veterinarian->phone_e164 = $administrativeDivision->phoneNumber($veterinarian->phone, format: PhoneFormat::E164);
                $veterinarian->phone_national = $administrativeDivision->phoneNumber($veterinarian->phone, format: PhoneFormat::NATIONAL);
            }
        });
    }

    protected function formattedInlineAddress(): Attribute
    {
        return Attribute::get(
            fn () => app(AdministrativeDivision::class)->inlineAddress(
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
