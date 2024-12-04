<?php

namespace App\Jobs;

use App\Models\Admission;
use App\Models\Patient;
use App\Models\Team;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Queue\Queueable;

class DeleteTeam implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Team $team)
    {
        $this->team = $team;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->team->veterinarians->each->forceDelete();
        $this->team->settings->each->forceDelete();
        $this->team->extensions->each->forceDelete();
        $this->team->expenseCategories->each->forceDelete();
        $this->team->formulas->each->forceDelete();
        $this->team->customFields->each->forceDelete();
        $this->team->locations->each->forceDelete();
        // //$this->team->failedImports()->forceDelete();

        $this->team->users->each(fn ($user) => $this->team->removeUser($user));

        $this->team->incidents()->withTrashed()->get()->each(function ($incident) {
            $incident->communications->each->forceDelete();
            $incident->forceDelete();
        });

        Admission::where('team_id', $this->team->id)->cursor()->each(function ($admission) {
            $patient = $admission->patient;

            if ($patient instanceof Patient) {
                // $this->deleteSafely($patient->predictions);
                $this->deleteSafely($patient->exams()->withTrashed()->get());
                $this->deleteSafely($patient->locations);
                $this->deleteSafely($patient->rechecks()->withTrashed()->get());
                $this->deleteSafely($patient->prescriptions()->withTrashed()->get());
                $this->deleteSafely($patient->nutritionPlans()->withTrashed()->get());
                $this->deleteSafely($patient->careLogs()->withTrashed()->get());
                $this->deleteSafely($patient->expenseTransactions()->withTrashed()->get());
                $patient->banding?->forceDelete();
                $patient->morphometric?->forceDelete();
                $patient->necropsy?->forceDelete();

                $patient->labReports()->withTrashed()->get()->each(function ($labReport) {
                    $labReport->labResult->forceDelete();
                    $labReport->forceDelete();
                });

                $patient->delete();
                //$patient->rescuer->delete();
            }
        });

        Admission::where('team_id', $this->team->id)->delete();

        $this->team->people()->withTrashed()->get()->each(function ($person) {
            $person->donations->each->forceDelete();
            $person->forceDelete();
        });

        $this->team->delete();
    }

    public function deleteSafely($models)
    {
        try {
            $models->each->forceDelete();
        } catch (ModelNotFoundException $e) {
        }
    }
}
