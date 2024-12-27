<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;

class IdentifyUnrecognizedPatientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wrmd:identify-unrecognized-patients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempt to identify the unrecognized species.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Patient::select('id')
            ->whereUnrecognized()
            ->orderByDesc('patients.id')
            ->chunk(100, function ($patients) {
                $this->info($patients->count().' unrecognized patients to attempt identification.');
                $patients->each->attemptIdentification();
            });
    }
}
