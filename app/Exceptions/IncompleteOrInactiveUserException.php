<?php

namespace App\Exceptions;

use Exception;

class IncompleteOrInactiveUserException extends Exception
{
    public function render($request)
    {
        return response()->json(['message' => 'امکان صدور فاکتور برای این کاربر غیر فعال وجود ندارد'], 422);
    }
}
