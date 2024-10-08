<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Request;

class RecordNotOwned extends Exception implements Responsable
{
    public function __construct(public string|null $failedMessage = null)
    {
        $this->failedMessage = $failedMessage ?: __('There was a problem accessing that record. Please try again or contact support for help.');
    }

    public function toResponse($request)
    {
        if (Request::expectsJson()) {
            return new JsonResponse($this->failedMessage, 422);
        }

        return redirect()->to(app(UrlGenerator::class)->previous())
            ->with('notification.heading', 'Oops!')
            ->with('notification.text', $this->failedMessage)
            ->with('notification.style', 'danger');
    }
}
