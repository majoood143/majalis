<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveSensitiveHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next): Response
    {
        $response = $next($request);
        // List of headers to remove
        $headers = [
            'X-Powered-By',
            'Server',
            'X-AspNet-Version',
            'X-Runtime',
            'X-Generator',
        ];

        foreach ($headers as $header) {
            $response->headers->remove($header);
        }

        return $response;
    }
}
