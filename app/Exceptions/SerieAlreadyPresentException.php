<?php

namespace App\Exceptions;

use Monolog\Logger;
use Exception;

class SerieAlreadyPresentException extends Exception
{
    public function render($request)
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
