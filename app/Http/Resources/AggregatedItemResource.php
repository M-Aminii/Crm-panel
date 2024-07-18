<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AggregatedItemResource extends JsonResource
{
    public function toArray($request)
    {


        return [
            'description_product' => $this->description_product,
            'total_area' => $this->total_area,
            'total_quantity' => $this->total_quantity,
            'total_weight' => $this->total_weight,
            'price_unit' => number_format($this->price_unit),
            'price_discounted' => number_format($this->price_discounted),
            'value_added_tax' => number_format($this->value_added_tax),
            'total_price' => number_format($this->total_price) ,
            'description' => $this->description,
        ];
    }
}

