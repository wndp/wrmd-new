<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Locality\LocaleOptions;
use App\Domain\OptionsStore;
use App\Domain\Reporting\AnnualReports;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AdminLocalizationController extends Controller
{
    public function __invoke(LocaleOptions $options): Response
    {
        OptionsStore::merge($options);
        $annualReports = AnnualReports::locales();

        return Inertia::render('Admin/Localization', compact('annualReports'));
    }
}
