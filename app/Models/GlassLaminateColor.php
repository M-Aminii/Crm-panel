<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlassLaminateColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'english_name',
        'price'

    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
