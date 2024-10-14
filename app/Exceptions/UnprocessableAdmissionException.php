<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class UnprocessableAdmissionException extends Exception
{
    public function render($request): RedirectResponse
    {
        Log::critical($this->getMessage());

        return redirect()->route('patients.create')
            ->with('notification.heading', 'Oops!')
            ->with('notification.text', 'Something unexpected has gone wrong. The WRMD team has been notified.')
            ->with('notification.style', 'danger');
    }
}
