<?php

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Event\Code\Throwable;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OcrException extends Exception
{
    #[Pure] public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = $message;
        $this->code    = ResponseAlias::HTTP_CONFLICT;
    }
}
