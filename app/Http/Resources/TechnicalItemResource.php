<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TechnicalItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'quantity' => $this->over,
            'description' => $this->description,
            'index' => $this->index,
        ];
    }
}
