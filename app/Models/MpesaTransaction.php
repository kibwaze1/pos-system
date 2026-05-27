<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    protected $fillable = [
        'checkout_request_id', 'sale_id', 'phone', 'amount',
        'status', 'result_code', 'result_desc', 'mpesa_receipt_number',
        'transaction_date'
    ];
}
