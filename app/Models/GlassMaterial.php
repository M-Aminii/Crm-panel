<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlassMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'english_name',
    ];
}
