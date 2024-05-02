<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlassFinalStructure extends Model
{
    use HasFactory;

    protected $table = 'glass_final_structure';


    protected $fillable = [
        'user_id',
        'product_id',
        'product_number',
        'structure_data',
    ];


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($glassLayer) {
            $ProductNumber = static::where('product_id', $glassLayer->product_id)->count() + 1;
            $glassLayer->product_number = $ProductNumber;
        });
    }
    public function isCreatedBy(User $user)
    {
        return $this->user_id === $user->id;
    }

}
