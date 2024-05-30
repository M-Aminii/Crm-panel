<?php

namespace App\DTO;

use Illuminate\Support\Str;

class CustomerDTO
{

    public $user_id;

    public $name;
    public $registration_number;
    public $phone;
    public $mobile;
    public $type;
    public $postal_code;
    public $national_id;
    public $address;
    public $province_id;
    public $city_id;







    public function __construct(array $data)
    {
        $this->user_id = auth('api')->id();
        $this->name = $data['name'];
        $this->national_id =$data['national_id']?? null;
        $this->registration_number = $data['registration_number'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->mobile =$data['mobile'] ?? null;
        $this->type = $data['type'];
        $this->postal_code = $data['postal_code'] ??null;
        $this->address =$data['address']??null;
        $this->province_id = $data['province_id'] ??null;
        $this->city_id = $data['city_id'] ??null;

    }

}
