<?php
namespace App\Exceptions;

use Exception;
class SatinGlassDimensionException extends Exception
{
    protected $productIndex;
    protected $dimensionIndex;
    protected $height;
    protected $width;

    public function __construct($productIndex, $dimensionIndex, $height, $width, $code = 0, Exception $previous = null)
    {
        $this->productIndex = $productIndex;
        $this->dimensionIndex = $dimensionIndex;
        $this->height = $height;
        $this->width = $width;

        $message = "ابعاد شیشه ساتینا بیش از حد مجاز است. محصول: {$productIndex}, بعد: {$dimensionIndex}, ";

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

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getDetails()
    {
        return "محصول: {$this->productIndex}, بعد: {$this->dimensionIndex},";
    }
}





