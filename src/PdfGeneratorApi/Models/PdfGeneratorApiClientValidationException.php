<?php


namespace PdfGeneratorApi\Models;


use Throwable;

class PdfGeneratorApiClientValidationException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
