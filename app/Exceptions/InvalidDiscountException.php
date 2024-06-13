<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidDiscountException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     */
    public function __construct($message = 'کاربر اجازه وارد کردن این مقدار تخفیف را ندارد .')
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response([
            'message' => $this->getMessage()
        ], $this->getCode());
    }
}
