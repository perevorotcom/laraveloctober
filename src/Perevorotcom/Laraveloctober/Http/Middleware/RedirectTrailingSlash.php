<?php

namespace Perevorotcom\Laraveloctober\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class RedirectTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(config('laraveloctober.trailingSlash')) {
            if (!preg_match('/.+\/$/', $request->getRequestUri())) {
                return Redirect::to(rtrim($request->getRequestUri(), '/').'/', 301);
            }
        } else {
            if (preg_match('/.+\/$/', $request->getRequestUri())) {
                return Redirect::to(rtrim($request->getRequestUri(), '/'), 301);
            }
        }

        return $next($request);
    }
}
