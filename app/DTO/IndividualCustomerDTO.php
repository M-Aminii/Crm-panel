<?php

namespace App\DTO;

use Illuminate\Support\Str;

class IndividualCustomerDTO
{

    public $user_id;
    public $name;
    public $national_id;
    public $phone;
    public $mobile;
    public $postal_code;
    public $address;
    public $province_id;
    public $city_id;








    public function __construct(array $data)
    {
        $this->user_id = auth('api')->id();
        $this->name = $data['name'];
        $this->national_id =$data['national_id'];
        $this->phone = $data['phone'] ?? null;
        $this->mobile =$data['mobile'] ?? null;
        $this->postal_code = $data['postal_code'];
        $this->address =$data['address'];
        $this->province_id = $data['province_id'];
        $this->city_id = $data['city_id'];

    }

}
