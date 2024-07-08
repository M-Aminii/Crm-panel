<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'key','invoice_id', 'product_id', 'product_section_id' , 'description','description_json', 'image_path' ,'price'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productSection()
    {
        return $this->belongsTo(ProductSection::class);
    }

    public function technicalItems()
    {
        return $this->hasMany(TechnicalItem::class, 'type_id');
    }

    public function dimensionItems()
    {
        return $this->hasMany(DimensionItem::class, 'type_id');
    }

    public function finalOrderItems()
    {
        return $this->hasMany(FinalOrderItem::class, 'type_id');
    }


}
