<?php

namespace App\Exceptions;

use Exception;

class ChapterAlreadyPresentException extends Exception
{
    protected $message="chapter is already present in serie";
    public function render($request)
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
