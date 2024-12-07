<?php

namespace App\Http\Middleware;

use App\Events\CaseYearChanged;
use App\Models\Team;
use App\Support\Wrmd;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SetAdmissionKeysInSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (is_numeric($request->a)) {
            try {
                abort_unless(
                    $request->user()->switchTeam(Team::find($request->a)),
                    404
                );
            } catch (\InvalidArgumentException $e) {
                abort('404');
            }
        }

        if (is_year($request->change_year_to)) {
            event(new CaseYearChanged($request->change_year_to));
            session([
                'caseYear' => (int) $request->change_year_to,
                'caseId' => 1,
            ]);
        } elseif (is_year($request->y) && is_numeric($request->c)) {
            if (session('caseYear') !== (int) $request->y) {
                event(new CaseYearChanged((int) $request->y));
            }
            session([
                'caseYear' => (int) $request->y,
                'caseId' => (int) $request->c,
            ]);
        } elseif (! session()->has('caseYear')) {
            session([
                'caseYear' => (int) date('Y'),
                'caseId' => 1,
            ]);
        }

        Inertia::share(['caseYear' => session('caseYear')]);

        return $next($request);
    }
}
