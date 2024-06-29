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

    // تابع برای تعریف ارتباط با مدل CustomerRole
    public function customerRoles()
    {
        return $this->belongsToMany(CustomerRole::class, 'customer_has_role', 'customer_id', 'role_id');
    }

    // تابع برای تخصیص نقش به مشتری
    public function assignCustomerRole($roleId)
    {
        $role = CustomerRole::find($roleId);
        if ($role) {
            $this->customerRoles()->attach($roleId);
        } else {
            // مدیریت خطا در صورت عدم وجود نقش
            throw new \Exception("Role not found");
        }
    }

    // تابع برای حذف نقش از مشتری
    public function removeCustomerRole($roleId)
    {
        $role = CustomerRole::find($roleId);
        if ($role) {
            $this->customerRoles()->detach($roleId);
        } else {
            // مدیریت خطا در صورت عدم وجود نقش
            throw new \Exception("Role not found");
        }
    }
    ///تبدیل ایدی استان و شهر به نام

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
    }

}
