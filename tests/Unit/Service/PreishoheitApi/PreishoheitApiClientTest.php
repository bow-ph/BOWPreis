<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Service\PreishoheitApi;

use BOW\Preishoheit\Service\ErrorHandling\ErrorHandler;
use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PreishoheitApiClientTest extends TestCase
{
    private PreishoheitApiClient $apiClient;
    private MockHandler $mockHandler;
    private ErrorHandler $errorHandler;
    private Context $context;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $systemConfigService = $this->createMock(SystemConfigService::class);
        $systemConfigService->method('getString')
            ->willReturn('test-api-key');

        $this->errorHandler = $this->createMock(ErrorHandler::class);
        $this->context = Context::createDefaultContext();

        $this->apiClient = new PreishoheitApiClient($systemConfigService, $this->errorHandler);
    }

    public function testCreateJobSuccess(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['job_id' => 'test-job-id']))
        );

        $result = $this->apiClient->createJob('amazon', ['123'], 'testProductId', $this->context);
        $this->assertEquals('test-job-id', $result['job_id']);
    }

    public function testCreateJobFailure(): void
    {
        $this->mockHandler->append(
            new Response(400, [], json_encode(['error' => 'Invalid request']))
        );

        $this->expectException(PreishoheitApiException::class);
        $this->apiClient->createJob('amazon', ['123'], 'testProductId', $this->context);
    }
}
