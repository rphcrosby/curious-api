<?php

namespace App\Http\Middleware;

use Closure;
use LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware as LeagueOAuthMiddleware;

class OAuthMiddleware extends LeagueOAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $scopesString = null)
    {
        if (!app()->runningInConsole()) {
            return parent::handle($request, $next, $scopesString);
        }

        return $next($request);
    }
}
