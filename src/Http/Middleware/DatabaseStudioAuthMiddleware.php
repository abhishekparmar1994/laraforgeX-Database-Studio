<?php

declare(strict_types=1);

namespace Laraforge\DatabaseStudio\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DatabaseStudioAuthMiddleware
{
    /**
     * Handle an incoming request for Database Studio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authEnabled = config('database-studio.auth.enabled', true);

        if (!$authEnabled) {
            return $next($request);
        }

        // Check if user is authenticated in session
        if (session('database_studio_authenticated') === true) {
            return $next($request);
        }

        // Handle unauthenticated API/JSON requests
        if ($request->expectsJson() || $request->is(config('database-studio.api_prefix', 'api/v1/database-manager') . '*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please login to Database Studio first.',
            ], 401);
        }

        // Redirect Web requests to Database Studio Login page
        $loginUrl = url(config('database-studio.path', 'database-studio') . '/login');
        return redirect()->guest($loginUrl);
    }
}
