<?php

namespace App\Http\Middleware;

use App\Enums\SettingKey;
use App\Options\LocaleOptions;
use App\Support\Wrmd;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Wrmd::settings(SettingKey::LANGUAGE);

        if (is_string($locale) && array_key_exists($locale, LocaleOptions::$languages)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
