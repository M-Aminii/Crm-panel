<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id','type_id','key', 'description_product', 'total_area', 'total_quantity','total_weight', 'price_unit','price_discounted','value_added_tax', 'total_price', 'description'
    ];
}
