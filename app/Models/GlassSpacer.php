<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlassSpacer extends Model
{
    use HasFactory;

    protected $fillable = [
        'size',
        'price'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
