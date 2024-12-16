<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VoidedScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected $extensions = ['UnVoid', 'WithVoided', 'WithoutVoided', 'OnlyVoided'];

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNull('voided_at');
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the restore extension to the builder.
     *
     * @return void
     */
    protected function addUnVoid(Builder $builder)
    {
        $builder->macro('unVoid', function (Builder $builder) {
            $builder->withVoided();

            return $builder->update(['voided_at' => null]);
        });
    }

    /**
     * Add the with-voided extension to the builder.
     */
    protected function addWithVoided(Builder $builder): void
    {
        $builder->macro('withVoided', function (Builder $builder, $withVoided = true) {
            if (! $withVoided) {
                return $builder->withoutVoided();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-voided extension to the builder.
     */
    protected function addWithoutVoided(Builder $builder): void
    {
        $builder->macro('withoutVoided', function (Builder $builder) {
            $builder->withoutGlobalScope($this)->whereNull('voided_at');

            return $builder;
        });
    }

    /**
     * Add the only-voided extension to the builder.
     */
    protected function addOnlyVoided(Builder $builder): void
    {
        $builder->macro('onlyVoided', function (Builder $builder) {
            $builder->withoutGlobalScope($this)->whereNotNull('voided_at');

            return $builder;
        });
    }
}
