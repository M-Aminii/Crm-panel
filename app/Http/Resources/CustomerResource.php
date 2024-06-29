<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'national_id' => $this->national_id,
            'registration_number' => $this->registration_number,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'type' => $this->type,
            'status' => $this->status,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'province' => $this->province_id,
            'city' => $this->city,
            /*'province' => $this->province ? $this->province->name : null,
            'city' => $this->city ? $this->city->name : null,*/
        ];
    }
}


