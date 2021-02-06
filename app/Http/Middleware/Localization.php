<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $local = ($request->hasHeader('Locale')) ? $request->header('Locale') : 'en';
            App::setLocale($local);

        return $next($request);
    }
}
