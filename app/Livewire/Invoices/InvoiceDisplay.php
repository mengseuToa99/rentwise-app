<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;

class InvoiceDisplay extends Component
{
    public $invoice;
    public $invoiceId;
    public $isFlipped = false;
    
    public function mount($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        $this->loadInvoice();
    }
    
    public function loadInvoice()
    {
        $this->invoice = Invoice::with(['rental', 'rental.tenant', 'rental.unit', 'rental.unit.property'])
            ->findOrFail($this->invoiceId);
    }
    
    public function toggleFlip()
    {
        $this->isFlipped = !$this->isFlipped;
    }
    
    public function render()
    {
        return view('livewire.invoices.invoice-display')
            ->layout('components.layouts.app', ['preserveSidebar' => true]);
    }
}
