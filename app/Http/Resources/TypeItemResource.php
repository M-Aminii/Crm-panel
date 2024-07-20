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
            'product_section' => new ProductSectionResource($this->whenLoaded('productSection')),
            'description' => $this->description,
            'description_json' => json_decode($this->description_json),
            'price' => $this->price,
            'image_path'=>$this->image_path,
            'description_structure'=>$this->description_structure,
            'dimensions' => DimensionItemResource::collection($this->whenLoaded('dimensionItems')),
            'total_quantity' => $this->totalQuantity,
            'total_all_area' => round ($this->totalALLArea,3),
            'total_environment' => round ($this->totalEnvironment,3),
            'technical_details' => TechnicalItemResource::collection($this->whenLoaded('technicalItems')),
        ];
    }
}

