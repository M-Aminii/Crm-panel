<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescriptionDimension extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'percent', 'price'
    ];

    public function dimensionItems()
    {
        return $this->belongsToMany(DimensionItem::class, 'dimension_description_items', 'description_id', 'dimension_id');
    }


}
