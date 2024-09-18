<?php

namespace App\Paginators;

use App\Caches\QueryCache;
use App\Models\Admission;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

class SearchResultPaginator extends Paginator
{
    protected $admission;

    protected $currentKey;

    protected $currentYear;

    protected $currentCase;

    protected $prevYear;

    protected $prevCase;

    protected $nextYear;

    protected $nextCase;

    protected $total;

    public function __construct(Admission $admission)
    {
        $this->admission = $admission;

        if (! $this->hasSearchResults()) {
            parent::__construct([], 0);
        } else {
            $this->constructSearchPaginator();
        }
    }

    private function constructSearchPaginator()
    {
        $this->resolveUrlCaseNumbers();

        Paginator::currentPageResolver(function ($pageName) {
            $page = $this->currentKey ? $this->currentKey + 1 : Request::input($pageName);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }

            return 1;
        });

        $page = Paginator::resolveCurrentPage('search');

        $collection = Collection::make(QueryCache::results()->caseNumbers)
            ->skip($page - 1)
            ->take(2);

        parent::__construct($collection, 1, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'search',
        ]);
    }

    /**
     * Determine if search results exist.
     */
    public function hasSearchResults(): bool
    {
        return QueryCache::exists() && QueryCache::results()->count > 1;
    }

    /**
     * Get the total number of items being paginated.
     */
    public function total(): int
    {
        return QueryCache::results()->count;
    }

    /**
     * Get the URL for the current page.
     */
    public function currentPageUrl(): ?string
    {
        return $this->url($this->currentPage())."&y={$this->currentYear}&c={$this->currentCase}";

        // [
        //     'y' => $this->currentYear,
        //     'c' => $this->currentCase
        // ]
    }

    /**
     * Get the URL for the previous page.
     */
    public function previousPageUrl(): ?string
    {
        return parent::previousPageUrl()."&y={$this->prevYear}&c={$this->prevCase}";
    }

    /**
     * Get the URL for the next page.
     */
    public function nextPageUrl(): ?string
    {
        return parent::nextPageUrl()."&y={$this->nextYear}&c={$this->nextCase}";
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'total' => $this->total,
        ]);
        // return [
        //     'current_page' => $this->currentPage(),
        //     'data' => $this->items->toArray(),
        //     'first_page_url' => $this->url(1),
        //     'from' => $this->firstItem(),
        //     'next_page_url' => $this->nextPageUrl(),
        //     'path' => $this->path(),
        //     'per_page' => $this->perPage(),
        //     'prev_page_url' => $this->previousPageUrl(),
        //     'to' => $this->lastItem(),
        // ];
    }

    /**
     * Resolve the case year and case id to append to the previous and next urls.
     */
    private function resolveUrlCaseNumbers(): void
    {
        $cases = QueryCache::results();

        $this->currentKey = array_search($this->admission->patient_id, $cases->patientIds);

        $nextKey = $this->currentKey === $cases->count ? $this->currentKey : $this->currentKey + 1;
        $prevKey = $this->currentKey === 0 ? 0 : $this->currentKey - 1;

        [$currentYear, $currentCase] = isset($cases->caseNumbers[$this->currentKey]) ? explode('-', $cases->caseNumbers[$this->currentKey]) : ['', ''];
        [$prevYear, $prevCase] = isset($cases->caseNumbers[$prevKey]) ? explode('-', $cases->caseNumbers[$prevKey]) : ['', ''];
        [$nextYear, $nextCase] = isset($cases->caseNumbers[$nextKey]) ? explode('-', $cases->caseNumbers[$nextKey]) : ['', ''];

        $this->currentYear = $currentYear;
        $this->currentCase = $currentCase;
        $this->prevYear = $prevYear;
        $this->prevCase = $prevCase;
        $this->nextYear = $nextYear;
        $this->nextCase = $nextCase;
        $this->total = count($cases->patientIds);
    }
}
