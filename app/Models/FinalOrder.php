<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalOrder extends Model
{
    protected $fillable = [
        'invoice_id', 'user_id', 'customer_id', 'serial_number', 'delivery_date',
        'sent_to_factory', 'sent_to_customer', 'informal_invoice_date', 'formal_invoice_date',
        'delivery_time', 'pre_payment', 'before_delivery', 'cheque'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
