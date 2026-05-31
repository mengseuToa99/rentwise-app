<?php

namespace App\Livewire\SimpleMode;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SimpleMarkPaid extends Component
{
    /** Custom partial amount entered per invoice, keyed by invoice id. */
    public array $amount = [];

    /** Short confirmation message shown after an action. */
    public string $flash = '';

    public function mount(): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }

        // Keep the simplified (locked) chrome while viewing this page.
        session(['simple_mode' => true]);
    }

    /**
     * Load an invoice and make sure the signed-in landlord actually owns it.
     * Returns null (and flashes an error) when not allowed.
     */
    protected function ownedInvoice($invoiceId): ?Invoice
    {
        $invoice = Invoice::with('rental')->find($invoiceId);

        if (!$invoice || !$invoice->rental || (int) $invoice->rental->landlord_id !== (int) Auth::id()) {
            $this->flash = __('app.simple_mode.pay_not_allowed');
            return null;
        }

        return $invoice;
    }

    /** Mark the whole remaining balance as paid. */
    public function payFull($invoiceId): void
    {
        $invoice = $this->ownedInvoice($invoiceId);
        if (!$invoice) {
            return;
        }

        $outstanding = round((float) $invoice->outstanding, 2);
        if ($outstanding > 0) {
            $invoice->recordPayment($outstanding, ['notes' => 'Simple Mode: paid in full']);
        }

        $this->flash = __('app.simple_mode.pay_done_full');
    }

    /** Record a custom amount the landlord typed in. */
    public function payCustom($invoiceId): void
    {
        $invoice = $this->ownedInvoice($invoiceId);
        if (!$invoice) {
            return;
        }

        $entered = round((float) ($this->amount[$invoiceId] ?? 0), 2);
        $outstanding = round((float) $invoice->outstanding, 2);

        // Ignore junk and never let them overpay past the balance.
        $amount = min(max($entered, 0), $outstanding);

        if ($amount <= 0) {
            $this->flash = __('app.simple_mode.pay_enter_amount');
            return;
        }

        $invoice->recordPayment($amount, ['notes' => 'Simple Mode: partial payment']);
        unset($this->amount[$invoiceId]);

        $this->flash = __('app.simple_mode.pay_done_partial', ['amount' => number_format($amount, 2)]);
    }

    /** Cancel an invoice the landlord no longer wants to collect. */
    public function cancelInvoice($invoiceId): void
    {
        $invoice = $this->ownedInvoice($invoiceId);
        if (!$invoice) {
            return;
        }

        $invoice->payment_status = 'cancelled';
        $invoice->save();

        $this->flash = __('app.simple_mode.pay_done_cancel');
    }

    public function render()
    {
        // Everything still owed to this landlord (pending / partial / overdue),
        // newest first, with the bits the cards need eager-loaded.
        $invoices = Invoice::query()
            ->whereHas('rental', fn ($q) => $q->where('landlord_id', Auth::id()))
            ->whereIn('payment_status', ['pending', 'partial', 'overdue'])
            ->with([
                'rental.tenant:user_id,first_name,last_name',
                'rental.unit:room_id,room_number',
            ])
            ->orderByDesc('issue_date')
            ->orderByDesc('invoice_id')
            ->get();

        $cards = $invoices->map(function (Invoice $invoice) {
            $tenant = $invoice->rental?->tenant;
            $customer = trim(($tenant?->first_name ?? '') . ' ' . ($tenant?->last_name ?? ''));

            $total = (float) $invoice->amount_due;
            $paid = (float) $invoice->amount_paid;

            return [
                'id' => $invoice->invoice_id,
                'room' => $invoice->rental?->unit?->room_number ?: '—',
                'customer' => $customer !== '' ? $customer : '—',
                'total' => $total,
                'paid' => $paid,
                'outstanding' => max(0, round($total - $paid, 2)),
                'status' => $invoice->payment_status,
            ];
        });

        return view('livewire.simple-mode.simple-mark-paid', [
            'cards' => $cards,
            'outstandingTotal' => (float) $cards->sum('outstanding'),
        ]);
    }
}
