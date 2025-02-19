<?php declare(strict_types=1);

namespace BOW\Preishoheit\Exception;

use Symfony\Component\HttpFoundation\Response;

class ConfigurationException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
