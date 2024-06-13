<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiscount extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'max_discount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
