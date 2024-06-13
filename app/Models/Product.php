<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'english_name', 'image_path'];

    public function sections()
    {
        return $this->hasMany(ProductSection::class);
    }
}
