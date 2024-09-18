<?php

namespace App\Http\Controllers\Hotline;

use App\Domain\Hotline\HotlineOptions;
use App\Domain\Hotline\HotlineSearch;
use App\Domain\OptionsStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HotlineSearchController extends Controller
{
    /**
     * Return the view to search hotline records.
     */
    public function create(HotlineOptions $options): Response
    {
        OptionsStore::add($options, 'hotline');

        return Inertia::render('Hotline/Search/Create');
    }

    /**
     * Return a list of the searched hotline records.
     */
    public function search(Request $request): Response
    {
        $incidents = HotlineSearch::run(Auth::user()->current_account, $request);

        return Inertia::render('Hotline/Search/Index', compact('incidents'));
    }
}
