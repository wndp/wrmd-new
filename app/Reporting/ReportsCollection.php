<?php

namespace App\Reporting;

use App\Enums\Extension;
use App\Models\Team;
use App\Reporting\Contracts\Report;
use App\Reporting\Reports\Admin\DeleteableAccounts;
use App\Reporting\Reports\Admin\InactiveAccounts;
use App\Reporting\Reports\BandingMorphometrics\BanditAuxiliary;
use App\Reporting\Reports\BandingMorphometrics\BanditBands;
use App\Reporting\Reports\BandingMorphometrics\BanditRecapture;
use App\Reporting\Reports\DailyTasks;
use App\Reporting\Reports\Disposition\DispositionsByDate;
use App\Reporting\Reports\Disposition\ReleaseTypesBySpecies;
use App\Reporting\Reports\Disposition\TotalDispositionsBySpecies;
use App\Reporting\Reports\Disposition\TransferTypesBySpecies;
use App\Reporting\Reports\Expenses\ExpenseStatement;
use App\Reporting\Reports\Homecare\HomecareHoursByCaregiver;
use App\Reporting\Reports\Homecare\PatientsSentToHomecare;
use App\Reporting\Reports\Location\DeceasedPatientsByLocation;
use App\Reporting\Reports\Location\{PendingPatientsByLocation};
use App\Reporting\Reports\Overview\AdmissionsByLocationFound;
use App\Reporting\Reports\Overview\AdmissionsPerYearBySpecies;
use App\Reporting\Reports\Overview\DailySummary;
use App\Reporting\Reports\Overview\DatesOfFirstBabies;
use App\Reporting\Reports\Overview\PatientsDaysInCare;
use App\Reporting\Reports\PatientMedicalRecord;
use App\Reporting\Reports\PatientReports\NecropsyReport;
use App\Reporting\Reports\PatientsList;
use App\Reporting\Reports\People\Donors;
use App\Reporting\Reports\People\FrugalRescuers;
use App\Reporting\Reports\People\Members;
use App\Reporting\Reports\People\People;
use App\Reporting\Reports\People\ReportingParties;
use App\Reporting\Reports\People\Rescuers;
use App\Reporting\Reports\People\Volunteers;
use App\Reporting\Reports\Prescriptions\ControlledSubstance;
use App\Reporting\Reports\Prescriptions\PrescriptionLabels;
use App\Reporting\Reports\Prescriptions\PrescriptionsDueByLocation;
use App\Reporting\Reports\Prescriptions\PrescriptionsDueByPatient;
use App\Support\ExtensionManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportsCollection extends Collection
{
    public static $reports = null;

    public function clearCache()
    {
        static::$reports = null;
    }

    public static function register(): static
    {
        if (!empty(static::$reports)) {
            return static::$reports;
        }

        $collection = new static();

        $collection->push(new ReportGroup(__('Overview'), [
            DailySummary::class,
            AdmissionsPerYearBySpecies::class,
        ], [
            AdmissionsByLocationFound::class,
            DatesOfFirstBabies::class,
            PatientsDaysInCare::class,
        ]));

        $collection->push(new ReportGroup(__('Disposition'), [
            TotalDispositionsBySpecies::class,
            DispositionsByDate::class,
        ], [
            ReleaseTypesBySpecies::class,
            TransferTypesBySpecies::class,
        ]));

        $collection->push(new ReportGroup(__('Location'), [
            PendingPatientsByLocation::class,
            DeceasedPatientsByLocation::class,
        ]));

        $collection->push(new ReportGroup('Prescriptions', [
            PrescriptionsDueByPatient::class,
            PrescriptionsDueByLocation::class,
        ], [
            ControlledSubstance::class,
        ]));

        $collection->push(
            (new ReportGroup('Prescriptions', [
                PrescriptionLabels::class,
            ]))->setVisibility(false)
        );

        if (ExtensionManager::isActivated(Extension::BANDING_MORPHOMETRICS)) {
            $collection->push(new ReportGroup('Banding and Morphometrics', [
                BanditBands::class,
                BanditAuxiliary::class,
            ], [
                BanditRecapture::class,
            ]));
        }

        if (ExtensionManager::isActivated(Extension::EXPENSES)) {
            $collection->push(new ReportGroup('Expenses', [
                ExpensesByCategorySummary::class,
                ExpensesByCategoryDetail::class,
            ], []));
        }

        $collection->push(new ReportGroup(__('Homecare'), [
            HomecareHoursByCaregiver::class,
            PatientsSentToHomecare::class,
        ]));

        $collection->push(new ReportGroup(__('People'), [
            People::class,
            Rescuers::class,
            FrugalRescuers::class,
        ], [
            Volunteers::class,
            Members::class,
            Donors::class,
            ReportingParties::class,
        ]));

        $collection->push(
            (new ReportGroup('Invisible', [
                DailyTasks::class,
                PatientsList::class,
                PatientMedicalRecord::class,
            ]))->setVisibility(false)
        );

        $collection->push(
            (new ReportGroup('Patient Reports', [
                NecropsyReport::class,
                ExpenseStatement::class,
            ]))->setPatientReports(true)->setVisibility(false)
        );


        if (Auth::check() && Auth::user()->can('viewWrmdAdmin')) {
            //$account = Auth::user()->current_account;

            $collection->push(
                (new ReportGroup('Account Reports', [
                    DeleteableAccounts::class,
                    InactiveAccounts::class,
                ]))//->setPatientReports(false)
            );

            // return new ReportGroup('Account Reports', [
            //     new DeleteableAccounts($account),
            //     new InactiveAccounts($account),
            // ]);
        }

        // collect((array) event(new RegisterReports()))
        //     ->filter(function ($group) {
        //         return $group instanceof ReportGroup;
        //     })
        //     ->each(function ($group) use ($collection) {
        //         $collection->push($group);
        //     });

        return static::$reports = $collection->sortBy('priority')->values();
    }

    public function initializeAll(Team $team)
    {
        return $this->map(function ($group) use ($team) {
            $instantiatedReports = [];

            foreach ($group->reports as $i => $reportColumn) {
                foreach ($reportColumn as $report) {
                    $instantiatedReports[$i][] = $this->initialize($report, $team);
                }
            }

            $group->reports = $instantiatedReports;

            return $group;
        });
    }

    /**
     * Initialize a report object if it is not already.
     *
     * @param  \App\Reporting\Contracts\Report|string  $report
     */
    public function initialize($report, Team $team): Report
    {
        if (is_a($report, Report::class)) {
            return $report;
        }

        return new $report($team);
    }

    public function pluckFavorites()
    {
        $favorites = [];

        $this->each(function ($group) use (&$favorites) {
            foreach ($group->reports as $reportColumn) {
                foreach ($reportColumn as $report) {
                    if ($report->isFavorited()) {
                        $favorites[] = $report;
                    }
                }
            }
        });

        return $favorites;
    }

    public function pluckPatientReports($account)
    {
        return $this->filter(function ($reportGroup) {
            return $reportGroup->patientReports;
        })
            ->instantiate($account);
    }
}
