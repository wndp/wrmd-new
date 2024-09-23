<?php

namespace App\Concerns;

use App\Enums\DailyTaskSchedulable;
use Symfony\Component\HttpFoundation\Response;
use UnhandledMatchError;

trait GetSchedulableFromResource
{
    public function getSchedulable($resource, $resourceId)
    {
        try {
            return DailyTaskSchedulable::tryFromUriKey($resource)
                ->model()::query()
                ->findOrFail($resourceId);
        } catch (UnhandledMatchError $e) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }
}
