<?php

namespace App\Exceptions;

use Exception;

class IncompleteOrInactiveCustomerException extends Exception
{
    public function render($request)
    {
        return response()->json(['message' => 'امکان صدور فاکتور برای این مشتری وجود ندارد'], 403);
    }
}
