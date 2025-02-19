<?php declare(strict_types=1);

namespace BOW\Preishoheit\Exception;

use Symfony\Component\HttpFoundation\Response;

class ApiVerificationException extends \Exception
{
    public function __construct(string $message = "", int $code = Response::HTTP_BAD_REQUEST, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
