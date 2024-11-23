<?php

namespace App\Http\Controllers\Oiled;

use App\Concerns\GetsCareLogs;
use App\Enums\AttributeOptionName;
use App\Enums\MediaCollection;
use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Repositories\OptionsStore;
use App\Support\Wrmd;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CaseSummaryController extends Controller
{
    use GetsCareLogs;

    public function __invoke(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();
        //$admission->patient->load('vitals', 'oilProcessing');

        OptionsStore::add([
            //new LocaleOptions(),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::EXAM_WEIGHT_UNITS->value,
                AttributeOptionName::EXAM_TEMPERATURE_UNITS->value,
                AttributeOptionName::EXAM_ATTITUDES->value,
            ])
        ]);

        return Inertia::render('Oiled/CaseSummary', [
            'patient' => $admission->patient,
            'logs' => fn () => $this->getCareLogs($admission->patient, Auth::user(), (Wrmd::settings(SettingKey::LOG_ORDER) === 'desc')),
            'media' => fn () => $admission->patient->getMedia(MediaCollection::EVIDENCE_PHOTO->value),
        ]);
    }
}
