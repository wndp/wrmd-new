<?php

namespace App\Models;

use App\Concerns\InteractsWithMedia;
use App\Concerns\QueriesDateRange;
use App\Concerns\QueriesOneOfMany;
use App\Concerns\ValidatesOwnership;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExpenseTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;
    use HasVersion7Uuids;

    protected $fillable = [
        'transacted_at',
        'memo',
        'debit',
        'credit',
    ];

    protected $casts = [
        'transacted_at' => 'date',
        'debit' => 'integer',
        'credit' => 'integer',
    ];

    protected $appends = [
        'transacted_at_for_humans',
        'debit_for_humans',
        'credit_for_humans',
    ];

    protected $with = [
        'category',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    protected function transactedAtForHumans(): Attribute
    {
        return Attribute::get(
            fn () => $this->transacted_at->toFormattedDateString(),
        );
    }

    /**
     * Set the debit attribute as an integer value and set the credit to 0.
     */
    public function setDebitAttribute(?float $value)
    {
        if (! is_numeric($value) || $value <= 0) {
            return;
        }

        $this->attributes['debit'] = $value * 100;
        $this->attributes['credit'] = 0;
    }

    /**
     * Get the debit attribute as an float.
     *
     * @param  float  $value
     */
    public function getDebitForHumansAttribute()
    {
        return number_format($this->debit / 100, 2);
    }

    /**
     * Set the credit attribute as an integer value and set the debit to 0.
     */
    public function setCreditAttribute(?float $value)
    {
        if (! is_numeric($value) || $value <= 0) {
            return;
        }

        $this->attributes['credit'] = $value * 100;
        $this->attributes['debit'] = 0;
    }

    /**
     * Get the credit attribute as an float.
     *
     * @param  float  $value
     */
    public function getCreditForHumansAttribute()
    {
        return number_format($this->credit / 100, 2);
    }

    /**
     * Get the debit or credit attribute as an float.
     *
     * @param  float  $value
     */
    public function getChargeForHumansAttribute()
    {
        $charge = $this->debit === 0 ? $this->credit : $this->debit;
        $charge = number_format($charge / 100, 2);

        return $this->debit !== 0 ? "($$charge)" : "$$charge";
    }
}
