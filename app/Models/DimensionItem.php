<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'key','invoice_id', 'type_id', 'height', 'width', 'weight','quantity', 'over'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function typeItem()
    {
        return $this->belongsTo(TypeItem::class, 'type_id');
    }
    public function descriptionDimensions()
    {
        return $this->belongsToMany(DescriptionDimension::class, 'dimension_description_items', 'dimension_id', 'description_id');
    }
}

