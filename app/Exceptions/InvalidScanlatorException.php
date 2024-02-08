<?php

namespace App\Exceptions;

use Exception;

class InvalidScanlatorException extends Exception
{
    public function render($request)
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
