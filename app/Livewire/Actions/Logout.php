<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Traits\LogsActivity;

class Logout
{
    use LogsActivity;

    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        $user = Auth::user();
        
        // Log the logout activity before logging out
        if ($user) {
            $this->logActivity('logout', "User logged out", $user->user_id);
        }
        
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
