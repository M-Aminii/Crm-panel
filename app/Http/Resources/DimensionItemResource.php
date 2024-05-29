<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DimensionItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'key' =>$this->key,
            'height' => $this->height,
            'width' => $this->width,
            'quantity' => $this->quantity,
            "over" =>$this->over,
            'description' => $this->description
        ];
    }
}

