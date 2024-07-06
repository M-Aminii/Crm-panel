<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $table = 'access';

    protected $fillable = ['user_id', 'max_discount','payment_terms','min_pre_payment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
