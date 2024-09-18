<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Silber\Bouncer\Bouncer;

class ScopeBouncer
{
    /**
     * Constructor.
     */
    public function __construct(protected Guard $auth, protected Bouncer $bouncer)
    {
        $this->auth = $auth;
        $this->bouncer = $bouncer;
    }

    /**
     * Set the proper Bouncer scope for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            return $next($request);
        }

        $teamId = $request->user()->current_team_id;

        $this->bouncer->scope()->to($teamId)
            ->onlyRelations()
            ->dontScopeRoleAbilities();

        return $next($request);
    }
}
