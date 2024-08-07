<?php
namespace App\Exceptions;

use Exception;
class WeightExceededException extends Exception
{
    protected $productIndex;
    protected $dimensionIndex;
    protected $weight;

    public function __construct($productIndex, $dimensionIndex, $weight, $message = "وزن این بعد بیش از حد مجاز است", $code = 0, Exception $previous = null)
    {
        $this->productIndex = $productIndex;
        $this->dimensionIndex = $dimensionIndex;
        $this->weight = $weight;
        parent::__construct($message, $code, $previous);
    }

    public function getProductIndex()
    {
        return $this->productIndex;
    }

    public function getDimensionIndex()
    {
        return $this->dimensionIndex;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}



