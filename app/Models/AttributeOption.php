<?php

namespace App\Models;

use App\Enums\AttributeOptionName;
use App\Options\Options;
use App\Support\AttributeOptionsCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class AttributeOption extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'name' => AttributeOptionName::class,
        'value' => 'string',
        'value_lowercase' => 'string',
        'sort_order' => 'integer',
    ];

    protected $fillable = [
        'name',
        'value',
        'value_lowercase',
        'sort_order',
    ];

    public function attributeOptionUiBehaviors(): HasMany
    {
        return $this->hasMany(AttributeOptionUiBehavior::class);
    }

    public function newCollection(array $models = []): AttributeOptionsCollection
    {
        return new AttributeOptionsCollection($models);
    }

    public static function getDropdownOptions(array $attributeOptionNames): AttributeOptionsCollection
    {
        return static::select([
                    'name',
                    'id',
                    'value',
                ])
                ->whereIn('name', $attributeOptionNames)
                ->orderBy('sort_order')
                ->orderBy('value')
                ->get()
                ->groupBy('name')
                //->map(fn ($attributeOptions) => $attributeOptions->pluck('value', 'id')->toArray())
                ->map(fn ($attributeOptions) => $attributeOptions->mapWithKeys(fn ($attributeOption) => [
                    $attributeOption->id => __($attributeOption->value)
                ])->toArray());

        return Cache::remember(
            'getDropdownOptions.'.App::getLocale().'.'.md5(json_encode($attributeOptionNames)),
            Carbon::now()->addMinutes(10),
            fn () =>
                static::select([
                    'name',
                    'id',
                    'value',
                ])
                ->whereIn('name', $attributeOptionNames)
                ->orderBy('sort_order')
                ->orderBy('value')
                ->get()
                ->groupBy('name')
                //->map(fn ($attributeOptions) => $attributeOptions->pluck('value', 'id')->toArray())
                ->map(fn ($attributeOptions) => $attributeOptions->mapWithKeys(fn ($attributeOption) => [
                    $attributeOption->id => __($attributeOption->value)
                ])->toArray())
        );
    }
}
