<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class MaintenanceIndex extends Component
{
    public function render()
    {
        $user = Auth::user();
        $isLandlord = $user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        });

        $query = MaintenanceRequest::query()
            ->with(['property', 'room'])
            ->latest();

        if ($isLandlord) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('landlord_id', $user->user_id);
            });
        } else {
            $query->where('tenant_id', $user->user_id);
        }

        return view('livewire.maintenance.maintenance-index', [
            'maintenanceRequests' => $query->get(),
            'isLandlord' => $isLandlord
        ]);
    }
} 