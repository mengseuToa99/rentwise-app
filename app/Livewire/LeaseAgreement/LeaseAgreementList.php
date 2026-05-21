<?php

namespace App\Livewire\LeaseAgreement;

use Livewire\Component;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaseAgreementList extends Component
{
    public function downloadAgreement($rentalId)
    {
        $agreement = Rental::findOrFail($rentalId);

        if (Auth::id() !== $agreement->tenant_id) {
            abort(403, 'Unauthorized action.');
        }

        $path = $agreement->agreement_file_path ?: $agreement->lease_agreement;

        if (!$path || !Storage::exists($path)) {
            session()->flash('error', 'Agreement file not found.');
            return;
        }

        return Storage::download($path, 'lease_agreement.pdf');
    }

    public function render()
    {
        $user = Auth::user();

        $agreements = Rental::where('tenant_id', $user->user_id)
            ->with(['property', 'room'])
            ->latest()
            ->get();

        return view('livewire.lease-agreement.lease-agreement-list', [
            'agreements' => $agreements,
        ]);
    }
}
