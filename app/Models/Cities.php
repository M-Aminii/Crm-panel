<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;

    protected $fillable = [ 'name','province_id'];

    public function Province()
    {
        return $this->belongsTo(Province::class);
    }
}
