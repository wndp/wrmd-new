<?php

namespace App\Reporting\Contracts;

use App\Jobs\QueuedReportGeneration;
use App\Models\Patient;
use App\Models\Team;
use App\Reporting\ApplyFilter;
use App\Reporting\Filters\TeamFilter;
use App\Reporting\Generators\ExcelX;
use App\Reporting\Generators\HeadlessChrome;
use App\Reporting\Generators\NullGenerator;
use App\Reporting\Generators\Zebra;
use App\Reporting\LinkToZebraReport;
use App\Reporting\Reports\AssetReport;
use App\Support\Wrmd;
use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\View\View;
use JsonSerializable;

abstract class Report implements Arrayable, Jsonable, JsonSerializable
{
    use SerializesModels;

    /**
     * The team the report belongs to.
     *
     * @var \App\Models\Team
     */
    public $team;

    /**
     * The patient to report on.
     *
     * @var \App\Models\Patient
     */
    public $patient;

    /**
     * The request to report on.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * The device that's requesting the report.
     *
     * @var string
     */
    public $deviceUuid;

    /**
     * The applied filters.
     *
     * @var \Illuminate\Support\Collection
     */
    public $appliedFilters;

    /**
     * Globally used team filter.
     *
     * @var TeamFilter
     */
    public $teamFilter;

    /**
     * Should the report generate in a queue?
     *
     * @var bool
     */
    protected $shouldQueue = true;

    /**
     * Constructor.
     */
    public function __construct(Team $team)
    {
        $this->teamFilter = new TeamFilter($this, $team);

        $this->setTeam($team);

        $this->appliedFilters = new Collection();
    }

    /**
     * String-ify the report.
     *
     * @return array
     */
    public function __toString()
    {
        return $this->jsonSerialize();
    }

    /**
     * Handle dynamic method calls.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($this->shouldQueue && in_array(strtolower($method), ['pdf', 'export', 'zpl'])) {
            QueuedReportGeneration::dispatch($this, $method);

            return new NullGenerator($this);
        } elseif (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $parameters);
        }

        throw new BadMethodCallException("Call to undefined method [{$method}].");
    }

    /**
     * Get the path to the reports view file.
     */
    abstract public function viewPath(): string;

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return Wrmd::humanize($this);
    }

    /**
     * Get the slug title of the report.
     */
    public function slug(): string
    {
        return Str::slug($this->title());
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return '';
    }

    /**
     * Get the reports render options.
     */
    public function options(): array
    {
        return [];
    }

    /**
     * Get the reports attributes that should be used when creating a link to the report.
     */
    public function filters(): Collection
    {
        return (new Collection())->when($this->team?->is_master_account, function ($collection) {
            return $collection->prepend($this->teamFilter);
        });
    }

    /**
     * Filters to provide to all reports.
     */
    public function globalFilters(): Collection
    {
        return Collection::make();

        return Collection::make()->when(
            $this->team?->is_master_account,
            fn ($collection) => $collection->prepend($this->teamFilter)
        );
    }

    /**
     * All filters for the report.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allFilters()
    {
        return $this->globalFilters()->merge(
            $this->filters()
        );
    }

    /**
     * Set the patient to report on.
     *
     * @return $this
     */
    public function patient(Patient|array $patient)
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * Determine if the report is an asset report.
     */
    public function isAsset(): bool
    {
        return $this instanceof AssetReport;
    }

    /**
     * Determine if the report is a zebra report.
     */
    public function isZebra(): bool
    {
        return $this instanceof ZebraReport;
    }

    /**
     * Determine if the report can be favorited.
     */
    public function canFavorite(): bool
    {
        return true;
    }

    /**
     * Determine if the report can be exported.
     */
    public function canExport(): bool
    {
        return $this instanceof ExportableReport && $this->canExport;
    }

    /**
     * Set the report to generate in a queue.
     *
     * @return void
     */
    public function shouldQueue()
    {
        $this->shouldQueue = true;

        return $this;
    }

    /**
     * Set the report to not generate in a queue.
     *
     * @return void
     */
    public function shouldNotQueue()
    {
        $this->shouldQueue = false;

        return $this;
    }

    /**
     * Set the request data.
     *
     * @return $this
     */
    public function withRequest(Request $request)
    {
        $this->request = new Fluent(array_merge(
            $request->all(),
            [
                'ajax' => $request->ajax(),
            ]
        ));

        return $this;
    }

    /**
     * The users device id that is requesting the report.
     *
     * @param  string  $deviceUuid
     * @return $this
     */
    public function fromDevice($deviceUuid)
    {
        $this->deviceUuid = $deviceUuid;

        return $this;
    }

    /**
     * Generate the report with the provided collection of ApplyFilter.
     *
     * @param  \Illuminate\Support\Collection  $filters
     * @return $this
     */
    public function withFilters(Collection $appliedFilters)
    {
        $this->appliedFilters = $appliedFilters;

        return $this;
    }

    /**
     * Apply any applicable filters to the reports query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyFilters(Builder $query, array $except = [])
    {
        $this->appliedFilters->filter(function ($appliedFilter) use ($except) {
            return ! in_array(get_class($appliedFilter->filter), $except);
        })
            ->values()->each->__invoke($this->request, $query);

        return $query;
    }

    /**
     * Get a filters value.
     *
     * @param  \App\Reporting\Contracts\Filter  $filter
     * @param  mixed  $default
     * @return mixed
     */
    public function getAppliedFilterValue($filter, $default = null)
    {
        $appliedFilter = $this->appliedFilters->first(function ($applyFilter) use ($filter) {
            return $applyFilter->filter instanceof $filter;
        });

        if ($appliedFilter instanceof ApplyFilter) {
            return $appliedFilter->value ?? $appliedFilter->filter->default();
        }

        return $default ?? (new $filter())->default();
    }

    /**
     * Get the reports HTML render-able view.
     *
     * @param  string  $view
     */
    public function view(array $mergeData = [], $view = null): View
    {
        return ViewFacade::make($view ?? $this->viewPath(), $this->viewData($mergeData));
    }

    /**
     * Get the raw un-merged data.
     */
    public function rawData(): array
    {
        return $this->data();
    }

    /**
     * Merge the data to be passed to the report view.
     */
    public function viewData(array $mergeData = []): array
    {
        if ($this->team->is_master_account && $this->teamFilter) {
            $this->teamFilter->setTeamOnReport();
        }

        return array_merge(
            $this->options(),
            $this->data(),
            ['title' => $this->title(), 'team' => $this->team],
            $mergeData
        );
    }

    /**
     * Get the reports key. Used to generate links to the report.
     *
     * @return string
     */
    public function key()
    {
        return Wrmd::uriKey($this);
    }

    /**
     * Determine if the report is favorited.
     *
     * @return bool
     */
    public function isFavorited()
    {
        return in_array($this->key(), (array) Wrmd::settings('favoriteReports', []));
    }

    /**
     * Generate a URL to the report.
     *
     * @return string
     */
    public function url(array $parameters = [])
    {
        if ($this->isAsset()) {
            return $this->viewPath();
        }

        if ($this->isZebra()) {
            return new LinkToZebraReport($this->title(), $this->key(), $parameters);
        }

        return URL::route('reports.show', array_merge([$this->key()], $parameters));
    }

    /**
     * Generate an HTML link to generated a report.
     *
     * @return \Illuminate\Support\HtmlString
     */
    // public function linkToGenerateReportXXX($format, $title = null, $patientId = null)
    // {
    //     return link_to_route(
    //         'reports.generate',
    //         $title ?? $this->title(),
    //         array_merge([
    //             $this->key(),
    //             'format' => $format,
    //             'admission' => $patientId,
    //         ], $otherAttributes)
    //     );
    // }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the report into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the report instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->title(),
            'explanation' => $this->explanation(),
            'titleSlug' => $this->slug(),
            'key' => $this->key(),
            'isFavorited' => $this->isFavorited(),
            'canExport' => $this->canExport(),
            'canFavorite' => $this->canFavorite(),
            'isAsset' => $this->isAsset(),
            'filters' => $this->allFilters(),
            'url' => $this->url(),
        ];
    }

    /**
     * Get the reports data.
     */
    abstract protected function data(): array;

    /**
     * Make the PDF version of the report.
     *
     * @return \App\Reporting\Contracts\Generator
     */
    protected function pdf(): Generator
    {
        return tap($this->pdfGenerator(), function ($generator) {
            $generator->handle();
        });
    }

    /**
     * Make the Excel version of the report.
     *
     * @return \App\Reporting\Contracts\Generator
     */
    protected function export(): Generator
    {
        return tap($this->exportGenerator(), function ($generator) {
            $generator->handle();
        });
    }

    /**
     * Make the Zebra report.
     *
     * @return \App\Reporting\Contracts\Generator
     */
    protected function zpl(): Generator
    {
        return tap(new Zebra($this), function ($generator) {
            $generator->handle();
        });
    }

    /**
     * Get the generator used to create the pdf version the report.
     *
     * @return \App\Reporting\Contracts\Generator
     */
    protected function pdfGenerator(): Generator
    {
        return new HeadlessChrome($this);
    }

    /**
     * Get the generator used to create the spreadsheet version the report.
     *
     * @return \App\Reporting\Contracts\Generator
     */
    protected function exportGenerator(): Generator
    {
        return new ExcelX($this);
    }

    /**
     * Set the team on the report.
     */
    protected function setTeam(Team $team)
    {
        $team->unsetRelation('extensions');
        $team->unsetRelation('settings');

        $this->team = $team;

        return $this;
    }

    /**
     * Get the reports key. Used to generate links to the report.
     *
     * @return string
     */
    public static function staticKey()
    {
        return Wrmd::uriKey(class_basename(get_called_class()));
    }
}
