<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\UnSpoofAccounts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminMaintenanceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Maintenance');
    }

    /**
     * Dispatch the requested maintenance method.
     */
    public function store(string $method): RedirectResponse
    {
        $result = match ($method) {
            'unspoof' => UnSpoofAccounts::dispatch(),
            'unrecognized' => Artisan::queue('wrmd:unrecognized'),
            'misidentified' => Artisan::queue('wrmd:misidentified'),
            'geocode' => Artisan::queue('wrmd:geocode'),
            'unlock' => Artisan::queue('wrmd:unlock', ['--all' => true]),
            default => null,
        };

        return back()
            ->with('notification.heading', 'Success!')
            ->with('notification.text', "The $method maintenance script is in the queue!");
    }
}
