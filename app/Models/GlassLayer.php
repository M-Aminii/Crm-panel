<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlassLayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'layer_number',
        'type_id',
        'width_id',
        'material_id',
    ];


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($glassLayer) {
            $layerNumber = static::where('product_id', $glassLayer->product_id)->count() + 1;
            $glassLayer->layer_number = $layerNumber;
        });
    }

}
