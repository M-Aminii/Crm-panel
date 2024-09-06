<?php

namespace App\Exceptions;

use Exception;

class LaminatedColoredGlassDimensionException extends Exception
{
    protected $productIndex;
    protected $dimensionIndex;
    protected $width;

    public function __construct($productIndex, $dimensionIndex, $width, $code = 0, Exception $previous = null)
    {
        $this->productIndex = $productIndex;
        $this->dimensionIndex = $dimensionIndex;
        $this->width = $width;

        $message = "عرض شیشه لمینت رنگی بیش از حد مجاز است. محصول: {$productIndex}, بعد: {$dimensionIndex}, عرض: {$width}";

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

    public function getWidth()
    {
        return $this->width;
    }

    public function getDetails()
    {
        return "محصول: {$this->productIndex}, بعد: {$this->dimensionIndex}, عرض: {$this->width}";
    }
}

