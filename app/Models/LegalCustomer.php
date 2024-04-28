<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalCustomer extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'user_id',
        'national_id',
        'registration_number',
        'phone',
        'mobile',
        'postal_code',
        'address',
        'province_id',
        'city_id'
    ];

}
