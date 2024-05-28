<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TypeItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'product' => new ProductResource($this->whenLoaded('product')),
            'description' => $this->description,
            'price' => $this->price,
            'technical_details' => DimensionItemResource::collection($this->whenLoaded('dimensionItems')),
            'dimensions' => TechnicalItemResource::collection($this->whenLoaded('technicalItems')),
        ];
    }
}

