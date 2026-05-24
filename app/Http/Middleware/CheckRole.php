<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

            // Admins are superusers: they may access any role-gated route. This
            // matches how the data layer already treats admins (see the
            // hasRole('admin') checks in MeterReadingQuery and the utility filters).
            if (!in_array('admin', $requiredRoles, true)) {
                $requiredRoles[] = 'admin';
            }

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

            // Soft-redirect tenants to their dashboard instead of slapping them with 403
            $isTenant = DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
                ->where('user_roles.user_id', $userId)
                ->where('roles.role_name', 'tenant')
                ->exists();

            if ($isTenant) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', "That area is for landlords only.");
            }

            return abort(403, 'Unauthorized. You need one of these roles: ' . implode(', ', $requiredRoles));
            
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('CheckRole error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return abort(500, 'Server error while checking permissions');
        }
    }
} 