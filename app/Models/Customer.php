<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'user_id',
        'national_id',
        'registration_number',
        'phone',
        'mobile',
        'type',
        'status',
        'postal_code',
        'address',
        'province_id',
        'city_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    ///تبدیل ایدی استان و شهر به نام
    /*
    protected $appends = ['province_name', 'city_name'];
    public function province()
    {
        return $this->belongsTo(province::class);
    }
    public function city()
    {
        return $this->belongsTo(Cities::class);
    }

    public function getProvinceNameAttribute()
    {
        return $this->province ? $this->province->name : null;
    }

    public function getCityNameAttribute()
    {
        return $this->city ? $this->city->name : null;
    }*/

}
