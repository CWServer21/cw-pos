<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable =[
        "purchase_id", "user_id", "sale_id", "cash_register_id", "account_id","payment_receiver", "payment_reference", "amount", "currency_id", "exchange_rate", "payment_at", "used_points", "change", "paying_method", "payment_proof", "document", "payment_note"
    ];

    protected $casts = [
        'payment_at' => 'datetime',
    ];
}
