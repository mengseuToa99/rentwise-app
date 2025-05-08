<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission  The required permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Check if user has the required permission
        if ($user->hasPermission($permission)) {
            return $next($request);
        }
        
        // Log the permission denial
        Log::warning('Permission denied', [
            'user_id' => $user->user_id,
            'permission' => $permission,
            'url' => $request->url()
        ]);
        
        // If it's an Ajax request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'Permission denied',
                'message' => 'You do not have the required permission: ' . $permission
            ], 403);
        }
        
        // Otherwise redirect with an error message
        return abort(403, 'You do not have permission to access this resource: ' . $permission);
    }
}
