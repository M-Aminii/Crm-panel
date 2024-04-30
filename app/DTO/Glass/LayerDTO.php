<?php

namespace App\DTO\Glass;


class LayerDTO
{
    public int $product_id;
    public int $type_id;
    public int $width_id;
    public int $material_id;

    public function __construct(array $data)
    {
        $this->product_id =  $data['product_id'];
        $this->type_id =  $data['type_id'];
        $this->width_id =  $data['width_id'];
        $this->material_id =  $data['material_id'];
    }
}

