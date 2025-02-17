<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\ErrorHandling;

use BOW\Preishoheit\Entity\ErrorLog\ErrorLogEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

class ErrorHandler
{
    private EntityRepository $errorLogRepository;

    public function __construct(EntityRepository $errorLogRepository)
    {
        $this->errorLogRepository = $errorLogRepository;
    }

    public function handleError(string $productId, string $errorType, string $errorMessage, Context $context): void
    {
        $this->errorLogRepository->create([
            [
                'id' => Uuid::randomHex(),
                'productId' => $productId,
                'errorType' => $errorType,
                'errorMessage' => $errorMessage
            ]
        ], $context);
    }
}
