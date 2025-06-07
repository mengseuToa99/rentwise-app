<?php

namespace App\Livewire\LeaseAgreement;

use Livewire\Component;
use App\Models\LeaseAgreement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaseAgreementList extends Component
{
    public function downloadAgreement($agreementId)
    {
        $agreement = LeaseAgreement::findOrFail($agreementId);
        
        // Check if user has permission to download
        if (Auth::id() !== $agreement->tenant_id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$agreement->agreement_file_path || !Storage::exists($agreement->agreement_file_path)) {
            session()->flash('error', 'Agreement file not found.');
            return;
        }

        return Storage::download($agreement->agreement_file_path, 'lease_agreement.pdf');
    }

    public function render()
    {
        $user = Auth::user();
        
        $agreements = LeaseAgreement::where('tenant_id', $user->user_id)
            ->with(['property', 'room'])
            ->latest()
            ->get();

        return view('livewire.lease-agreement.lease-agreement-list', [
            'agreements' => $agreements
        ]);
    }
} 