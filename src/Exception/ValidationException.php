<?php declare(strict_types=1);

namespace BOW\Preishoheit\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BOW_PREISHOHEIT__VALIDATION_ERROR';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
