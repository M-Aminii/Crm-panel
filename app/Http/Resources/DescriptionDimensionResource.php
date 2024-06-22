<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DescriptionDimensionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'percent'=> $this->percent ?? null,
            'price'=> $this->price ?? null,
        ];
    }
}

