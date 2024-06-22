<?php
namespace App\Exceptions;

namespace App\Exceptions;

use Exception;

class DimensionException extends Exception
{
    protected $productIndex;
    protected $dimensionIndex;

    public function __construct($productIndex, $dimensionIndex, $message = "امکان استفاده از این ابعاد برای", $code = 0, Exception $previous = null)
    {
        $this->productIndex = $productIndex;
        $this->dimensionIndex = $dimensionIndex;
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
}

