<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckModulePermission
{
    public function handle(Request $request, Closure $next, $module)
    {
        if (auth()->user()->role->name === 'admin') {
            return $next($request);
        }
        if (!auth()->user()->hasPermission($module)) {
            abort(403, 'You do not have permission to access this module.');
        }
        return $next($request);
    }
}
