<?php
namespace App\Http\Resources;

use App\Services\InvoiceService;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeItemResource extends JsonResource
{

    private $totalQuantity = 0;
    public function toArray($request)
    {
        $invoiceService = new InvoiceService();


        foreach ($this->dimensionItems as $dimensionItems) {
            $this->totalQuantity += $dimensionItems->quantity;
        }

        foreach ($this->dimensionItems as $dimensionItems) {
            $this->totalALLArea += $invoiceService->CalculateArea($dimensionItems->height,$dimensionItems->width) * $dimensionItems->quantity ;
        }


        foreach ($this->dimensionItems as $dimensionItems) {
            $this->totalEnvironment += $invoiceService ->CalculateEnvironment($dimensionItems->height,$dimensionItems->width ,$dimensionItems->quantity);
        }

        return [
            'key' =>$this->key,
            'product' => new ProductResource($this->whenLoaded('product')),
            'description' => $this->description,
            'price' => $this->price,
            'dimensions' => DimensionItemResource::collection($this->whenLoaded('dimensionItems')),
            'total_quantity' => $this->totalQuantity,
            'total_all_area' => round ($this->totalALLArea,3),
            'total_environment' => round ($this->totalEnvironment,3),
            'technical_details' => TechnicalItemResource::collection($this->whenLoaded('technicalItems')),
        ];
    }
}

