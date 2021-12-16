<?php

namespace Perevorotcom\Laraveloctober\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class RedirectTrailingSlash
{
    public function handle($request, Closure $next)
    {
        $url = parse_url($request->getRequestUri());

        if (config('laraveloctober.trailingSlash')) {
            if (!preg_match('/.+\/$/', $url['path'])) {
                $url = rtrim($url['path'], '/').'/'.(!empty($url['query']) ? '?'.$url['query'] : '');

                header('HTTP/1.1 301 Moved Permanently');
                header('Location: '.$url);
                exit;
            }
        } else {
            if (preg_match('/.+\/$/', $url['path'])) {
                $url = rtrim($url['path'], '/').(!empty($url['query']) ? '?'.$url['query'] : '');

                header('HTTP/1.1 301 Moved Permanently');
                header('Location: '.$url);
                exit;
            }
        }

        return $next($request);
    }
}
