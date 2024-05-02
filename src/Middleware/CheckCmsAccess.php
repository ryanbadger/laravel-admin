<?php

namespace RyanBadger\LaravelAdmin\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCmsAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Check if the user has the 'is_admin' attribute set to true
        if (!$user || !$user->is_admin) {
            abort(403, 'Unauthorized access to CMS.');
        }

        return $next($request);
    }
}
