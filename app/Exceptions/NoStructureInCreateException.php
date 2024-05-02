<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NoStructureInCreateException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     */
    public function __construct($message = 'هیچ ساختاری برای اضافه شدن وجود ندارد')
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
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
