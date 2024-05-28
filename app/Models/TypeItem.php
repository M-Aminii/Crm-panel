<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'product_id', 'description', 'price'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function technicalItems()
    {
        return $this->hasMany(TechnicalItem::class, 'type_id');
    }

    public function dimensionItems()
    {
        return $this->hasMany(DimensionItem::class, 'type_id');
    }
}
