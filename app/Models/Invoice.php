<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
