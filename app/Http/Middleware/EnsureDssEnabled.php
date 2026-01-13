<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureDssEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('dss.enabled', true)) {
            abort(404);
        }

        return $next($request);
    }
}
