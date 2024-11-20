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
        try {
            $this->{'Dispatch'.Str::studly($method)}();
            $message = "The $method maintenance script is in the queue!";
        } catch (\BadMethodCallException $e) {
            $message = "Unable to dispatch the $method maintenance script!";
        }

        return back()->with('flash.notification', $message);
    }

    /**
     * Dispatch the un-spoof accounts job.
     */
    private function DispatchUnSpoof()
    {
        dispatch(new UnSpoofAccounts());
    }

    /**
     * Dispatch the unrecognized job.
     */
    private function DispatchUnrecognizedd()
    {
        Artisan::queue('wrmd:unrecognized');
    }

    /**
     * Dispatch the misidentified job.
     */
    private function DispatchMisidentified()
    {
        Artisan::queue('wrmd:misidentified');
    }

    /**
     * Dispatch the geocode addresses job.
     */
    private function DispatchGeocode()
    {
        Artisan::queue('wrmd:geocode');
    }

    /**
     * Dispatch the unlock patients job.
     */
    private function DispatchUnlock()
    {
        Artisan::queue('wrmd:unlock', ['--all' => true]);
    }
}
