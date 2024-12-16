<?php

namespace App\Jobs\PatientNotifications;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Events\NotifyPatient;
use App\Models\Patient;
use App\Models\Team;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class HasWildAlertEvent implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public static function apiUrl()
    {
        return 'https://api.wildalert.org';
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Team $team, public Patient $patient)
    {
        $this->team = $team;
        $this->patient = $patient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
        ]);

        if ($this->patient->disposition_id === $dispositionPendingId && $this->patient->taxon) {
            $response = Http::retry(3, 100, throw: false)->get(static::apiUrl().'/alerts', [
                'genus' => $this->patient->taxon->genus,
                'species' => $this->patient->taxon->species,
                'latitude' => $this->patient->coordinates_found?->latitude,
                'longitude' => $this->patient->coordinates_found?->longitude,
            ]);

            if ($response->successful() && count($response->json('data')) > 0) {
                NotifyPatient::dispatch(
                    $this->patient,
                    __('Health Event'),
                    __('This patient may be part of a health event.'),
                    'danger'
                );
            }
        }
    }
}
