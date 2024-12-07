<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Point as geoPHPPoint;
use geoPHP;

class SingleStorePoint implements Castable
{
    public function __construct(public float $latitude, public float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class (static::class) implements CastsAttributes {
            public function __construct(private $point)
            {
                $this->point = $point;
            }

            public function get($model, string $key, $value, array $attributes): ?SingleStorePoint
            {
                if (! $value) {
                    return null;
                }

                return $this->point::fromWkt($value);
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
        };
    }

    public function toWkt(): string
    {
        $wktData = $this->getWktData();

        return "POINT({$wktData})";
    }

    public function getWktData(): string
    {
        return "{$this->longitude} {$this->latitude}";
    }

    public function toSqlExpression($value)
    {
        $wkt = $this->toWkt();

        return DB::raw("'$wkt'");
    }

    public static function fromWkt(string $wkt): static
    {
        $geometry = static::parseWkt($wkt);

        if (! ($geometry instanceof static)) {
            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, $geometry::class)
            );
        }

        return $geometry;
    }

    private static function parseWkt($wkt)
    {
        try {
            /** @var geoPHPGeometry|false $geoPHPGeometry */
            $geoPHPGeometry = geoPHP::load($wkt);
        } finally {
            if (! isset($geoPHPGeometry) || ! $geoPHPGeometry) {
                throw new InvalidArgumentException('Invalid spatial value');
            }
        }

        if ($geoPHPGeometry instanceof geoPHPPoint) {
            if ($geoPHPGeometry->coords[0] === null || $geoPHPGeometry->coords[1] === null) {
                throw new InvalidArgumentException('Invalid spatial value');
            }

            return new static($geoPHPGeometry->coords[1], $geoPHPGeometry->coords[0]);
        }
    }
}
