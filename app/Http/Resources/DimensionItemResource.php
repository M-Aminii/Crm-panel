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
            'area' => $invoiceService->CalculateArea($this->height,$this->width),
            'total area' => $invoiceService->CalculateArea($this->height,$this->width) * $this->quantity,
            'Environment' => $invoiceService ->CalculateEnvironment($this->height,$this->width ,$this->quantity),
            "over" =>$this->over,
            'description' => $this->description
        ];
    }
}

