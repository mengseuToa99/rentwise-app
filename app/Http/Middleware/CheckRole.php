<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            Log::debug('CheckRole: User not authenticated');
            return redirect()->route('login');
        }
        
        try {
            $userId = Auth::id();
            $requiredRoles = explode('|', $role);
            
            Log::debug('CheckRole: Starting role check', [
                'user_id' => $userId,
                'required_roles' => $requiredRoles
            ]);
            
            // Check roles directly in the database using a raw query
            $hasRole = DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
                ->where('user_roles.user_id', $userId)
                ->whereIn('roles.role_name', $requiredRoles)
                ->exists();
            
            if ($hasRole) {
                Log::debug('CheckRole: User has required role - access granted');
                return $next($request);
            }
            
            Log::debug('CheckRole: User lacks required roles - access denied');
            return abort(403, 'Unauthorized. You need one of these roles: ' . implode(', ', $requiredRoles));
            
        } catch (\Exception $e) {
            Log::error('CheckRole error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return abort(500, 'Server error while checking permissions');
        }
    }
} 