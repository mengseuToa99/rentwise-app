<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoice_details';
    protected $primaryKey = 'invoice_id';
    public $timestamps = true;

    protected $fillable = [
        'rental_id',
        'invoice_number',
        'amount_due',
        'amount_paid',
        'period_start',
        'period_end',
        'issue_date',
        'due_date',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'issue_date' => 'date',
        'due_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentHistory::class, 'invoice_id', 'invoice_id');
    }

    public function utilityUsages()
    {
        return $this->belongsToMany(UtilityUsage::class, 'invoice_utility_usages', 'invoice_id', 'usage_id')
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getOutstandingAttribute()
    {
        return max(0, (float) $this->amount_due - (float) $this->amount_paid);
    }

    /**
     * Record a payment against this invoice. Creates a payment_histories row,
     * bumps the running amount_paid total, and recomputes the payment_status
     * (pending → partial → paid / overdue). Wrapped in a transaction so the
     * ledger and the invoice total can never drift apart.
     */
    public function recordPayment(float $amount, array $attributes = []): PaymentHistory
    {
        return DB::transaction(function () use ($amount, $attributes) {
            $payment = $this->payments()->create([
                'recorded_by_user_id' => $attributes['recorded_by_user_id'] ?? auth()->id(),
                'payment_amount'      => $amount,
                'payment_date'        => $attributes['payment_date'] ?? now(),
                'payment_method'      => $attributes['payment_method'] ?? 'cash',
                'transaction_ref'     => $attributes['transaction_ref'] ?? null,
                'receipt_number'      => $attributes['receipt_number'] ?? null,
                'notes'               => $attributes['notes'] ?? null,
            ]);

            $this->amount_paid = (float) $this->amount_paid + $amount;
            $this->save();

            $this->recomputeStatus();

            return $payment;
        });
    }

    public function recomputeStatus(): void
    {
        $paid = (float) $this->amount_paid;
        $due  = (float) $this->amount_due;

        if ($paid <= 0) {
            $status = $this->due_date && now()->gt($this->due_date) ? 'overdue' : 'pending';
        } elseif ($paid >= $due) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        if ($this->payment_status !== $status) {
            $this->payment_status = $status;
            $this->save();
        }
    }

    public function scopePending($q)
    {
        return $q->whereIn('payment_status', ['pending', 'partial', 'overdue']);
    }

    public function scopePaid($q)
    {
        return $q->where('payment_status', 'paid');
    }

    public function scopeOverdue($q)
    {
        return $q->where('payment_status', 'overdue');
    }
}
