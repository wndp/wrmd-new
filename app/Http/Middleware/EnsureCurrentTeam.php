<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCurrentTeam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $teamId = Auth::user()->current_team_id;

        // Will re-visit spoofing / impersonating
        // if ($teamId === SpecialtyAccounts::WRMD) {
        //     session()->put('isSpoofing', false);
        // }

        if (! is_numeric($teamId)) {
            return to_route('choose_team.index');
        }

        return $next($request);
    }
}
