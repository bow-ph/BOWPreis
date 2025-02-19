<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Service\Mail;

use BOW\Preishoheit\Service\Mail\FailedUpdateMailService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Content\Mail\Service\MailService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class FailedUpdateMailServiceTest extends TestCase
{
    private EntityRepository $errorLogRepository;
    private MailService $mailService;
    private FailedUpdateMailService $failedUpdateMailService;
    private string $adminEmail = 'admin@example.com';

    protected function setUp(): void
    {
        $this->errorLogRepository = $this->createMock(EntityRepository::class);
        $this->mailService = $this->createMock(MailService::class);
        $this->failedUpdateMailService = new FailedUpdateMailService(
            $this->errorLogRepository,
            $this->mailService,
            $this->adminEmail
        );
    }

    public function testSendDailySummaryWithNoErrors(): void
    {
        $context = Context::createDefaultContext();
        $searchResult = $this->createMock(EntitySearchResult::class);
        
        $searchResult->method('count')->willReturn(0);
        
        $this->errorLogRepository
            ->method('search')
            ->willReturn($searchResult);

        $this->mailService
            ->expects($this->never())
            ->method('send');

        $this->failedUpdateMailService->sendDailySummary($context);
    }

    public function testSendDailySummaryWithErrors(): void
    {
        $context = Context::createDefaultContext();
        $searchResult = $this->createMock(EntitySearchResult::class);
        $error = new \stdClass();
        $error->createdAt = new \DateTime();
        $error->productId = 'test-product-id';
        $error->message = 'Test error message';
        
        $searchResult->method('count')->willReturn(1);
        $searchResult->method('getElements')->willReturn([$error]);
        
        $this->errorLogRepository
            ->method('search')
            ->with(
                $this->callback(function (Criteria $criteria) {
                    return $criteria->getFilters()->count() === 1;
                }),
                $context
            )
            ->willReturn($searchResult);

        $this->mailService
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function ($data) {
                    return $data['recipients'][$this->adminEmail] === 'Admin' &&
                           strpos($data['contentHtml'], 'Test error message') !== false;
                }),
                $context
            );

        $this->failedUpdateMailService->sendDailySummary($context);
    }
}
