<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SetGroupMiddleware
 */
class SetGroupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $groupId = session('group_id');

            if (!$groupId) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.group.no_group_associated_with_user'),
                ], 403);
            }
            setPermissionsTeamId($groupId);
        }

        return $next($request);
    }
}
