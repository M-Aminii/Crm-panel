<?php

namespace App\Http\Resources;

use App\Services\InvoiceService;
use Illuminate\Http\Resources\Json\JsonResource;

class DimensionItemResource extends JsonResource
{
    public function toArray($request)
    {
        $invoiceService = new InvoiceService();


        return [
            'key' =>$this->key,
            'height' => $this->height,
            'width' => $this->width,
            'quantity' => $this->quantity,
            'weight' => $this->weight,
            'area' => round ($invoiceService->CalculateArea($this->height,$this->width),3),
            'total_area' =>   round ($invoiceService->CalculateArea($this->height,$this->width) * $this->quantity,3),
            'Environment' =>  round ($invoiceService ->CalculateEnvironment($this->height,$this->width ,$this->quantity),3),
            'over' =>$this->over,
            'position' =>$this->position,
            'descriptions' => DescriptionDimensionResource::collection($this->whenLoaded('descriptionDimensions')),
        ];

    }
}

