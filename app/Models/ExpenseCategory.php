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

class ExpenseCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ValidatesOwnership;
    use HasVersion7Uuids;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function expenseTransactions(): HasMany
    {
        return $this->hasMany(ExpenseTransaction::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public static function findByName(string $name, Team $team): self
    {
        return static::whereName($name)->where(function ($query) use ($team) {
            $query->where(function ($query) {
                $query->whereNull('team_id')->whereNull('parent_id');
            })
                ->orWhere('team_id', $team->id);
        })
            ->firstOrFail();
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $like = Str::lower('%'.$search.'%');

        return $query->where(
            fn ($query) => $query->where('name', 'like', $like)->orWhere('description', 'like', $like)
        );
    }

    public function isParent(): bool
    {
        return $this->parent_id === null;
    }
}