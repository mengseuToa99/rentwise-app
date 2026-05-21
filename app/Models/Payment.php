<?php

namespace App\Models;

/**
 * Backwards-compat alias for PaymentHistory.
 * Old code referenced `Payment` but pointed at a non-existent `payment_detail` table —
 * this keeps existing imports working against the real `payment_histories` table.
 */
class Payment extends PaymentHistory
{
}
