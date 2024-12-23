<?php

namespace App\ValueObjects;

use App\Casts\SingleStorePointCast;
use geoPHP;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use Point as geoPHPPoint;
use Stringable;

class SingleStorePoint implements Castable, Jsonable, Stringable
{
    public function __construct(public float $latitude, public float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString(): string
    {
        return $this->toWkt();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this, $options | JSON_THROW_ON_ERROR);
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new SingleStorePointCast;
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
            $geoPHPGeometry = geoPHP::load($wkt, 'wkt');
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
