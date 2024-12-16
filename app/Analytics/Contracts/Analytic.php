<?php

namespace App\Analytics\Contracts;

use App\Analytics\AnalyticFilters;
use App\Analytics\Categories;
use App\Analytics\Concerns\SegmentsData;
use App\Analytics\Series;
use App\Models\Team;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use ReflectionObject;
use ReflectionProperty;

abstract class Analytic implements Jsonable, JsonSerializable
{
    use SegmentsData;

    protected $account;

    protected $filters;

    public $categories;

    public $series;

    /**
     * Constructor.
     *
     * @param  \App\Models\Team  $team
     * @param  \App\Domain\Analytics\AnalyticFilters  $filters
     */
    public function __construct($team, $filters)
    {
        $this->team = $team;
        $this->filters = $filters;

        $this->categories = new Categories;
        $this->series = new Series;

        $this->addSegmentationMacros();
    }

    /**
     * Static constructor.
     *
     * @return static
     */
    public static function analyze(Team $team, AnalyticFilters $filters)
    {
        ($static = new static($team, $filters))->compute();

        return $static;
    }

    /**
     * Compute the analytics results.
     *
     * @return void
     */
    abstract protected function compute();

    /**
     * Convert the analytic into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return collect((new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC))->reduce(function ($attributes, $property) {
            $attributes[$property->getName()] = $this->{$property->getName()};

            return $attributes;
        }, []);
    }

    /**
     * Get the analytic as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
