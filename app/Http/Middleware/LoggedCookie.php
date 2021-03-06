<?php

namespace App\Http\Middleware;

use Auth;
use Cookie;
use Closure;

class LoggedCookie
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Proceed if we do not have a redirect

        if (method_exists($response, 'getStatusCode') && $response->getStatusCode() === 429) {
            return $response;
        }

        if (get_class($response) !== 'Symfony\Component\HttpFoundation\RedirectResponse') {
            // If user is logged in, send a cookie
            // so HTTP caching reverse proxy can bypass the caching if needed

            if (Auth::check()) {
                $response->withCookie(Cookie::forever('logged', 'true'));
            } else {
                $response->withCookie(Cookie::forget('logged'));
            }
        }

        return $response;
    }
}
