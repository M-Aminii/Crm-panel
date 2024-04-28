<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualCustomer extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'name',
        'national_id',
        'phone',
        'mobile',
        'postal_code',
        'address',
        'province_id',
        'city_id'
    ];

}
