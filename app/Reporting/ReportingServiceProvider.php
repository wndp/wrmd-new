<?php

namespace App\Reporting;

use Api2Pdf\Api2Pdf;
// use App\Reporting\Contracts\PdfApiInterface;
// use App\Reporting\Engines\NullPdfEngine;
// use App\Reporting\Reports\Admin\DeleteableAccounts;
// use App\Reporting\Reports\Admin\InactiveAccounts;
// use App\Reporting\Reports\DailyTasks;
// use App\Reporting\Reports\Disposition\DispositionsByDate;
// use App\Reporting\Reports\Disposition\ReleaseTypesBySpecies;
// use App\Reporting\Reports\Disposition\TotalDispositionsBySpecies;
// use App\Reporting\Reports\Disposition\TransferTypesBySpecies;
// use App\Reporting\Reports\Homecare\HomecareHoursByCaregiver;
// use App\Reporting\Reports\Homecare\PatientsSentToHomecare;
// use App\Reporting\Reports\Location\DeceasedPatientsByLocation;
// use App\Reporting\Reports\Location\{PendingPatientsByLocation};
// use App\Reporting\Reports\Overview\AdmissionsByLocationFound;
// use App\Reporting\Reports\Overview\AdmissionsPerYearBySpecies;
// use App\Reporting\Reports\Overview\DailySummary;
// use App\Reporting\Reports\Overview\DatesOfFirstBabies;
// use App\Reporting\Reports\Overview\PatientsDaysInCare;
// use App\Reporting\Reports\PatientMedicalRecord;
// use App\Reporting\Reports\PatientsList;
// use App\Reporting\Reports\People\Donors;
// use App\Reporting\Reports\People\FrugalRescuers;
// use App\Reporting\Reports\People\Members;
// use App\Reporting\Reports\People\People;
// use App\Reporting\Reports\People\ReportingParties;
// use App\Reporting\Reports\People\Rescuers;
// use App\Reporting\Reports\People\Volunteers;
// use App\Reporting\Reports\Prescriptions\ControlledSubstance;
// use App\Reporting\Reports\Prescriptions\PrescriptionLabels;
// use App\Reporting\Reports\Prescriptions\PrescriptionsDueByLocation;
// use App\Reporting\Reports\Prescriptions\PrescriptionsDueByPatient;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ReportingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */

    /**
     * Register the service provider.
     */
    // public function register(): void
    // {
    //     $this->app->bind(PdfApiInterface::class, function () {
    //         if (config('wrmd.reporting.pdf_driver') === 'api2pdf') {
    //             return new Api2Pdf(config('services.api2pdf.key'));
    //         } else {
    //             return new NullPdfEngine();
    //         }
    //     });

    //     $this->app->singleton(ReportsCollection::class, function ($app) {
    //         $collection = new ReportsCollection();

    //         $collection->push(new ReportGroup(__('Overview'), [
    //             DailySummary::class,
    //             AdmissionsPerYearBySpecies::class,
    //         ], [
    //             AdmissionsByLocationFound::class,
    //             DatesOfFirstBabies::class,
    //             PatientsDaysInCare::class,
    //         ]));

    //         $collection->push(new ReportGroup(__('Disposition'), [
    //             TotalDispositionsBySpecies::class,
    //             DispositionsByDate::class,
    //         ], [
    //             ReleaseTypesBySpecies::class,
    //             TransferTypesBySpecies::class,
    //         ]));

    //         $collection->push(new ReportGroup(__('Location'), [
    //             PendingPatientsByLocation::class,
    //             DeceasedPatientsByLocation::class,
    //         ]));

    //         $collection->push(new ReportGroup('Prescriptions', [
    //             PrescriptionsDueByPatient::class,
    //             PrescriptionsDueByLocation::class,
    //         ], [
    //             ControlledSubstance::class,
    //         ]));

    //         $collection->push(
    //             (new ReportGroup('Prescriptions', [
    //                 PrescriptionLabels::class,
    //             ]))->setVisibility(false)
    //         );

    //         $collection->push(new ReportGroup(__('Homecare'), [
    //             HomecareHoursByCaregiver::class,
    //             PatientsSentToHomecare::class,
    //         ]));

    //         $collection->push(new ReportGroup(__('People'), [
    //             People::class,
    //             Rescuers::class,
    //             FrugalRescuers::class,
    //         ], [
    //             Volunteers::class,
    //             Members::class,
    //             Donors::class,
    //             ReportingParties::class,
    //         ]));

    //         $collection->push(
    //             (new ReportGroup('Invisible', [
    //                 DailyTasks::class,
    //                 PatientsList::class,
    //                 PatientMedicalRecord::class,
    //             ]))->setVisibility(false)
    //         );

    //         collect((array) event(new RegisterReports()))
    //             ->filter(function ($group) {
    //                 return $group instanceof ReportGroup;
    //             })
    //             ->each(function ($group) use ($collection) {
    //                 $collection->push($group);
    //             });

    //         return $collection->sortBy('priority')->values();
    //     });
    // }

    /**
     * Perform post-registration booting of services.
     */
    public function boot(Dispatcher $events): void
    {
        $events->listen(RegisterReports::class, function () {
            if (Auth::check()) {
                $account = Auth::user()->current_account;

                return (new ReportGroup(
                    __('Annual'),
                    AnnualReports::byLocale($account),
                    AnnualReports::byLocale($account, $account->subdivision)
                ))->setPriority(2);
            }
        });

        $events->listen(RegisterReports::class, function ($e) {
            if (Auth::check() && Auth::user()->can('displayAdmin')) {
                $account = Auth::user()->current_account;

                return new ReportGroup('Account Reports', [
                    new DeleteableAccounts($account),
                    new InactiveAccounts($account),
                ]);
            }
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [ReportsCollection::class, PdfApiInterface::class];
    }
}
