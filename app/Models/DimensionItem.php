<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_index','invoice_id', 'type_id', 'edge_type', 'glue_type', 'post_type',
        'delivery_date', 'frame', 'balance', 'vault_type', 'part_number',
        'map_dimension', 'map_view', 'vault_number', 'delivery_meterage',
        'order_number', 'usage', 'car_type'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function typeItem()
    {
        return $this->belongsTo(TypeItem::class, 'type_id');
    }
}

