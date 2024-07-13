<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'key','final_order_id','type_id', 'product_id', 'area', 'delivery_date', // افزودن delivery_date
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function finalOrder()
    {
        return $this->belongsTo(FinalOrder::class);
    }
    public function typeItem()
    {
        return $this->belongsTo(TypeItem::class, 'type_id');
    }


}
