<?php

namespace App\Exceptions;

use App\Concerns\LoadsAdmissionAndSharesPagination;
use App\Models\Admission;
use Exception;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PatientVoidedException extends Exception
{
    use LoadsAdmissionAndSharesPagination;

    public function __construct(public Admission $admission)
    {
        $this->admission = $admission;
    }

    /**
     * Render the response for when a Patient is voided.
     *
     * @param  \Illuminate\Http\Request
     */
    public function render($request)
    {
        $admissionsPaginator = $this->getAdmissionsPaginator(Auth::user()->currentTeam->id);
        $admission = $admissionsPaginator->first()->load(['patient' => fn ($builder) => $builder->onlyVoided()]);
        $listPaginationPage = $this->getListPaginationPage($admissionsPaginator, $admission);
        $searchPaginator = $this->getSearchResultPaginator($admission);

        $this->shareLastCaseId();

        return Inertia::render('Admissions/Voided', compact(
            'admission',
            'admissionsPaginator',
            'searchPaginator',
            'listPaginationPage'
        ));
    }
}
