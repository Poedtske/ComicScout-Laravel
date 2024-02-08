<?php

namespace App\Exceptions;

use Monolog\Logger;
use Exception;

class SerieAlreadyPresentException extends Exception
{
    protected $message="serie is already present in scanlator";
    public function render($request)
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
