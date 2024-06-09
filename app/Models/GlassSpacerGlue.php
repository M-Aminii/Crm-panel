<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlassSpacerGlue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','price'

    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
