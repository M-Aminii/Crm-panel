<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'key','invoice_id', 'type_id', 'height', 'width', 'weight','quantity', 'over', 'description'
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

