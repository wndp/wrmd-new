<?php

namespace App\Models;

use App\Enums\AttributeOptionUiBehavior as AttributeOptionUiBehaviorEnum;
use App\Options\Options;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class AttributeOptionUiBehavior extends Model
{
    use HasFactory;

    protected $casts = [
        'attribute_option_id' => 'integer',
        'behavior' => AttributeOptionUiBehaviorEnum::class,
    ];

    protected $fillable = [
        'attribute_option_id',
        'behavior',
    ];

    public function attributeOption(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class);
    }

    public static function getGroupedAttributeOptions(array $attributeOptionNames): array
    {
        return Cache::remember(
            'getGroupedAttributeOptions:'.md5(json_encode($attributeOptionNames)),
            Carbon::now()->addMinutes(10),
            function () use ($attributeOptionNames) {
                $attributeOptions = AttributeOption::whereIn('name', $attributeOptionNames)
                    ->withWhereHas('attributeOptionUiBehaviors')
                    ->get();

                $groupedBehaviors = [];

                foreach ($attributeOptions as $attributeOption) {
                    foreach ($attributeOption->attributeOptionUiBehaviors as $attributeOptionUiBehavior) {
                        if (empty($groupedBehaviors[$attributeOptionUiBehavior->behavior->value])) {
                            $groupedBehaviors[$attributeOptionUiBehavior->behavior->value] = [];
                        }

                        $groupedBehaviors[$attributeOptionUiBehavior->behavior->value][] = $attributeOption->id;
                    }
                }

                return $groupedBehaviors;
            }
        );
    }

    public static function getAttributeOptionUiBehaviorIds(array $attributeOptionCombinations): array
    {
        $attributeOptionCombinations = Options::isMultidimensional($attributeOptionCombinations)
            ? $attributeOptionCombinations
            : [$attributeOptionCombinations];

        $attributeOptionNames = array_unique(data_get($attributeOptionCombinations, '*.0'));
        $attributeOptionUiBehaviors = static::getGroupedAttributeOptions($attributeOptionNames);

        $result = [];

        foreach ($attributeOptionCombinations as $attributeOptionCombination) {
            $ids = data_get($attributeOptionUiBehaviors, $attributeOptionCombination[1], []);
            if (count($ids) === 1) {
                $result[] = data_get($ids, 0, -1);
            } else {
                $result[] = $ids;
            }
        }

        return $result;
    }
}
