<?php

namespace App\Http\Requests;

use App\Models\Patient;
use App\Reporting\ApplyFilter;
use App\Reporting\Contracts\Report;
use App\Reporting\FacilitatesReporting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportRequest extends FormRequest
{
    use FacilitatesReporting;

    protected $report;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'format' => [
                'required',
                'in:html,pdf,xlsx,zpl',
            ],
        ];
    }

    /**
     * Make the report instance for the given request.
     */
    public function report(string $key): Report
    {
        $report = $this->reportFromKey($key);

        abort_if(is_null($report), 404);

        $this->report = is_a($report, Report::class)
            ? $report->setAccount($this->user()->currentTeam)
            : new $report($this->user()->currentTeam);

        $this->setPatientOnReport();

        return $this->report->withRequest($this)->withFilters($this->filters());
    }

    /**
     * Set the patient on the report if it's in the request.
     */
    public function setPatientOnReport()
    {
        if ($this->filled('patientId')) {
            try {
                $patient = tap(
                    Patient::where('id', $this->patientId)->firstOrFail(),
                    fn ($patient) => $patient->validateOwnership(Auth::user()->current_team_id)
                );

                $this->report->patient($patient);
            } catch (ModelNotFoundException $e) {
                throw new HttpResponseException($this->patientNotOwnedResponse());
            }
        }
    }

    /**
     * Create a response for when a patient is not owned by the users account.
     */
    private function patientNotOwnedResponse()
    {
        $message = __('There was a problem generating the report for this patient.');

        if ($this->expectsJson()) {
            return new JsonResponse($message, 422);
        }

        return redirect()->to(
            app(UrlGenerator::class)->previous()
        )
            ->with('flash.style', 'danger')
            ->with('flash.notificationHeading', __('Oops!'))
            ->with('flash.notification', $message);
    }

    /**
     * Get the filters for the request.
     */
    public function filters(): Collection
    {
        $availableFilters = $this->availableFilters();

        if (empty($filters = $this->decodedFilters())) {
            return collect($availableFilters)->map(function ($filter) {
                return new ApplyFilter($filter, $filter->default());
            })->values();
        }

        return Collection::make($filters)->map(function ($filter) use ($availableFilters) {
            $matchingFilter = $availableFilters->first(function ($availableFilter) use ($filter) {
                return $filter['class'] === get_class($availableFilter);
            });

            if ($matchingFilter) {
                return ['filter' => $matchingFilter, 'value' => $filter['value']];
            }
        })->reject(function ($filter) {
            if (is_array($filter['value'])) {
                return count($filter['value']) < 1;
            } elseif (is_string($filter['value'])) {
                return trim($filter['value']) === '';
            }

            return is_null($filter['value']);
        })->map(function ($filter) {
            return new ApplyFilter($filter['filter'], $filter['value']);
        })->values();
    }

    /**
     * Decode the filters specified for the request.
     */
    protected function decodedFilters(): array
    {
        if (empty($this->filters)) {
            return [];
        }

        $filters = json_decode(base64_decode($this->filters), true);

        return is_array($filters) ? $filters : [];
    }

    /**
     * Get all of the possibly available filters for the request.
     */
    protected function availableFilters(): Collection
    {
        return (new $this->report(Auth::user()->currentTeam))->allFilters();
    }
}
