<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;

    protected $fillable = [ 'name','province_id'];
    protected $hidden = [
        'province_id',
        'created_at',
        'updated_at',
    ];

    /*public function customers()
    {
        return $this->hasMany(Customer::class);
    }*/
    public function Province()
    {
        return $this->belongsTo(Province::class);
    }
}
