<?php

namespace App\Exceptions;

use Exception;

class SpecialGlassDimensionException extends Exception
{
    protected $productIndex;
    protected $dimensionIndex;
    protected $height;
    protected $width;
    protected $glassType;

    public function __construct($productIndex, $dimensionIndex, $height, $width, $glassType, $code = 0, Exception $previous = null)
    {
        $this->productIndex = $productIndex;
        $this->dimensionIndex = $dimensionIndex;
        $this->height = $height;
        $this->width = $width;
        $this->glassType = $glassType;

        $message = "ابعاد شیشه {$glassType} بیش از حد مجاز است. محصول: {$productIndex}, بعد: {$dimensionIndex}, ارتفاع: {$height}، عرض: {$width}.";

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

    public function getGlassType()
    {
        return $this->glassType;
    }

    public function getDetails()
    {
        return "محصول: {$this->productIndex}, بعد: {$this->dimensionIndex}, نوع شیشه: {$this->glassType}, ارتفاع: {$this->height}، عرض: {$this->width}.";
    }
}
