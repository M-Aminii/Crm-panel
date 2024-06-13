<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'level'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_has_role', 'role_id', 'customer_id');
    }
}

