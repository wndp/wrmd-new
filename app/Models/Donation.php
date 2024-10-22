<?php

namespace App\Models;

use App\Concerns\QueriesDateRange;
use App\Models\AttributeOption;
use App\Repositories\AdministrativeDivision;
use App\Support\Timezone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Number;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Donation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use QueriesDateRange;
    use HasVersion7Uuids;
    use LogsActivity;

    protected $fillable = [
        'person_id',
        'donated_at',
        'value',
        'method_id',
        'comments',
    ];

    protected $casts = [
        'person_id' => 'string',
        'donated_at' => 'date',
        'value' => 'integer',
        'method_id' => 'integer',
        'comments' => 'string',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class, 'method_id');
    }

    protected function valueForHumans(): Attribute
    {
        $currencyCode = app(AdministrativeDivision::class)->countryCurrencyCode();

        return Attribute::get(
            fn () => Number::currency($this->value / 100, $currencyCode)
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
