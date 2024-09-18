<?php

namespace App\Exceptions;

use Exception;
use Inertia\Inertia;

class AdmissionNotFoundException extends Exception
{
    /**
     * Render the response for when an admission could not be found.
     *
     * @param  \Illuminate\Http\Request
     */
    public function render($request)
    {
        return Inertia::render('Admissions/404');
    }
}
