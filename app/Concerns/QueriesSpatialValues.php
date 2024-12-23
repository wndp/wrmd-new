<?php

namespace App\Concerns;

use App\ValueObjects\SingleStorePoint;
use Illuminate\Contracts\Database\Query\Expression as ExpressionContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Inspired by https://github.com/MatanYadaev/laravel-eloquent-spatial/blob/0a5a565797d87430a51bfda25c5e5e419e220fdd/src/Traits/HasSpatial.php
 */
trait QueriesSpatialValues
{
    public function scopeWhereDistanceSphere(
        Builder $query,
        ExpressionContract|SingleStorePoint|string $column,
        ExpressionContract|SingleStorePoint|string $geometryOrColumn,
        string $operator,
        int|float $value
    ): void {
        $query->whereRaw(
            sprintf(
                'GEOGRAPHY_DISTANCE(%s, %s) %s ?',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $operator,
            ),
            [$value],
        );
    }

    public function scopeOrderByDistanceSphere(
        Builder $query,
        ExpressionContract|SingleStorePoint|string $column,
        ExpressionContract|SingleStorePoint|string $geometryOrColumn,
        string $direction = 'asc'
    ): void {
        $query->orderByRaw(
            sprintf(
                'GEOGRAPHY_DISTANCE(%s, %s) %s',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $direction
            )
        );
    }

    protected function toExpressionString(ExpressionContract|SingleStorePoint|string $geometryOrColumnOrExpression): string
    {
        $grammar = $this->getGrammar();

        if ($geometryOrColumnOrExpression instanceof ExpressionContract) {
            $expression = $geometryOrColumnOrExpression;
        } elseif ($geometryOrColumnOrExpression instanceof SingleStorePoint) {
            $expression = DB::raw($geometryOrColumnOrExpression->toSqlExpression($this->getConnection())->getValue($grammar));
        } else {
            $expression = DB::raw($grammar->wrap($geometryOrColumnOrExpression));
        }

        return (string) $expression->getValue($grammar);
    }
}
