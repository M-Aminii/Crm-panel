<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlassLaminate extends Model
{
    use HasFactory;

    protected $fillable = [
        'size'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
