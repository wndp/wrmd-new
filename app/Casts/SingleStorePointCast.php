<?php

namespace App\Casts;

use App\ValueObjects\SingleStorePoint;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;

class SingleStorePointCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?SingleStorePoint
    {
        if (! $value) {
            return null;
        }

        return SingleStorePoint::fromWkt($value);
    }

    public function set($model, string $key, $value, array $attributes): ?ExpressionContract
    {
        if (! $value) {
            return null;
        }

        if (is_array($value)) {
            $value = Geometry::fromArray($value);
        }

        if ($value instanceof ExpressionContract) {
            return $value;
        }

        return $value->toSqlExpression($model->getConnection());
    }
}
