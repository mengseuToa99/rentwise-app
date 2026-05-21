<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    protected $table = 'payment_histories';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'invoice_id',
        'recorded_by_user_id',
        'payment_amount',
        'payment_date',
        'payment_method',
        'transaction_ref',
        'receipt_number',
        'reconciliation_status',
        'notes',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id', 'user_id');
    }
}
