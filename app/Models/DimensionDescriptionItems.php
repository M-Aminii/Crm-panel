<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensionDescriptionItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'dimension_id','description_id'
    ];


}
