<?php

namespace App\Http\Middleware;

use App\Enums\Plan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamIsOnProPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //temp
        return $next($request);

        abort_unless(
            $request->user()->currentTeam->sparkPlan()?->name === Plan::PRO->value,
            Response::HTTP_FORBIDDEN
        );

        return $next($request);
    }
}
