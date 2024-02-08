<?php

namespace App\Exceptions;

use Exception;

class InvalidScanlatorException extends Exception
{
    protected $message="This scanlator does not exist";
    public function render($request)
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
