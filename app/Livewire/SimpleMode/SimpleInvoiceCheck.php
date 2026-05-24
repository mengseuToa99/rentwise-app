<?php

namespace App\Livewire\SimpleMode;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SimpleInvoiceCheck extends Component
{
    public function mount(): void
    {
        // Keep the simplified (locked) chrome while viewing this page.
        session(['simple_mode' => true]);
    }

    public function render()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // This landlord's invoices issued in the current month, with everything
        // the cards need eager-loaded to avoid per-invoice round-trips.
        $invoices = Invoice::query()
            ->whereHas('rental', fn ($q) => $q->where('landlord_id', Auth::id()))
            ->whereBetween('issue_date', [$start->toDateString(), $end->toDateString()])
            ->with([
                'rental.tenant:user_id,first_name,last_name',
                'rental.unit:room_id,room_number',
                'utilityUsages.utility:utility_id,utility_name',
            ])
            ->orderByDesc('issue_date')
            ->orderByDesc('invoice_id')
            ->get();

        $cards = $invoices->map(function (Invoice $invoice) {
            $tenant = $invoice->rental?->tenant;
            $customer = trim(($tenant?->first_name ?? '') . ' ' . ($tenant?->last_name ?? ''));

            $utilities = $invoice->utilityUsages
                ->map(fn ($usage) => $usage->utility?->utility_name)
                ->filter()
                ->unique()
                ->values()
                ->all();

            return [
                'id' => $invoice->invoice_id,
                'room' => $invoice->rental?->unit?->room_number ?: '—',
                'customer' => $customer !== '' ? $customer : '—',
                'utilities' => $utilities,
                'total' => (float) $invoice->amount_due,
                'status' => $invoice->payment_status,
            ];
        });

        return view('livewire.simple-mode.simple-invoice-check', [
            'cards' => $cards,
            'monthTotal' => (float) $invoices->sum(fn ($i) => (float) $i->amount_due),
            'monthLabel' => $start->translatedFormat('F Y'),
        ]);
    }
}
