<?php

namespace App\Reporting\Contracts;

use App\Options\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class Filter implements JsonSerializable
{
    use SerializesModels;

    /**
     * Is the filter part of the report period group?
     *
     * @var bool
     */
    public $periodic = false;

    /**
     * Apply the filter to the given query.
     *
     * @param  mixed  $value
     */
    abstract public function apply(Fluent $request, Builder $query, $value): Builder;

    /**
     * The name of the Vue component to be used for this filter.
     */
    public function component(): string
    {
        return Str::slug(Str::snake(class_basename($this), ' '));
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return null;
    }

    /**
     * Get the filters render options.
     */
    public function options(): array
    {
        return [];
    }

    /**
     * Humanize the filter into a proper name.
     */
    public function name(): string
    {
        return Str::title(Str::snake(class_basename($this), ' '));
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'class' => get_class($this),
            'name' => $this->name(),
            'component' => $this->component(),
            'periodic' => $this->periodic,
            'options' => Options::arrayToSelectable($this->options()),
            // collect($this->options())->map(function ($text, $value) {
            //     return ['name' => $text, 'value' => $value];
            // })->values()->all(),
            'currentValue' => $this->default() ?? '',
        ];
    }

    /**
     * How to react when country is treated like a string.
     *
     * @return array
     */
    public function __toString()
    {
        return $this->jsonSerialize();
    }
}
