<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Models\Admission;
use Illuminate\Support\Facades\Auth;

class CustomFieldBuilder
{
    use AsAction;

    /**
     * The number of allowed custom fields to be created.
     */
    public const NUMBER_OF_ALLOWED_FIELDS = 15;

    /**
     * Build the custom field for the requested location.
     *
     * @param  Admission  $admission
     * @return Collection
     */
    public function handle($admission = null)
    {
        if ($admission instanceof Admission) {
            $admission->patient->load('customValues');
            $admission->patient->rescuer->load('customValues');
            $admission->patient->exams->load('customValues');
        }

        $accountId = $admission->account_id ?? Auth::user()->current_account_id;

        return CustomFieldsRepository::getCustomFields($accountId);
    }
}
