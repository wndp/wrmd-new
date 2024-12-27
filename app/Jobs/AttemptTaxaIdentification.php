<?php

namespace App\Jobs;

use App\Actions\SearchPreciseTaxa;
use App\Models\Patient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AttemptTaxaIdentification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Patient $patient, public bool $isMisidentified = false)
    {
        $this->patient = $patient;
        $this->isMisidentified = $isMisidentified;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->patient->isUnrecognized()) {
            return;
        }

        $result = SearchPreciseTaxa::run($this->patient->common_name);

        if ($this->isIdentified($result)) {
            $this->patient->taxon_id = $result->first()['taxon_id'];

            if ($this->isMisidentified && $this->isReIdentified($result)) {
                $this->patient->common_name = $result->first()['common_name'];
            }
        } else {
            $this->patient->taxon_id = null;
        }

        $this->patient->timestamps = false;
        $this->patient->saveQuietly();
    }

    /**
     * Determine if the common name was misidentified and re-identified.
     */
    private function isReIdentified(Collection $collection): bool
    {
        return $collection->count() === 1;
    }

    /**
     * Determine if the common name was identified.
     */
    private function isIdentified(Collection $collection): bool
    {
        return $collection->unique('taxon_id')->count() === 1;
    }
}
