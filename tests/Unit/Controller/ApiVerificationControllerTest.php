<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Controller;

use BOW\Preishoheit\Controller\ApiVerificationController;
use BOW\Preishoheit\Exception\ApiVerificationException;
use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVerificationControllerTest extends TestCase
{
    private ApiVerificationController $controller;
    private PreishoheitApiClient $apiClient;
    private LoggerInterface $logger;
    private Context $context;

    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(PreishoheitApiClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->context = $this->createMock(Context::class);
        
        $this->controller = new ApiVerificationController(
            $this->apiClient,
            $this->logger
        );
    }

    public function testVerifyApiKeySuccess(): void
    {
        $request = new Request([], ['apiKey' => 'valid-api-key']);
        
        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Starting API key verification'],
                ['API key verification successful']
            );

        $this->apiClient->expects($this->once())
            ->method('verifyApiKey')
            ->with($this->context)
            ->willReturn(true);

        $response = $this->controller->verifyApiKey($request, $this->context);
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getContent(), true));
    }

    public function testVerifyApiKeyMissing(): void
    {
        $request = new Request();
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Starting API key verification');

        $this->logger->expects($this->once())
            ->method('error')
            ->with('API key verification failed: API key is required');

        $response = $this->controller->verifyApiKey($request, $this->context);
        
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(
            ['success' => false, 'message' => 'API key is required'],
            json_decode($response->getContent(), true)
        );
    }

    public function testVerifyApiKeyVerificationFailed(): void
    {
        $request = new Request([], ['apiKey' => 'invalid-api-key']);
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Starting API key verification');

        $this->apiClient->expects($this->once())
            ->method('verifyApiKey')
            ->with($this->context)
            ->willThrowException(new ApiVerificationException('Invalid API key'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with('API key verification failed: Invalid API key');

        $response = $this->controller->verifyApiKey($request, $this->context);
        
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(
            ['success' => false, 'message' => 'Invalid API key'],
            json_decode($response->getContent(), true)
        );
    }

    public function testVerifyApiKeyUnexpectedError(): void
    {
        $request = new Request([], ['apiKey' => 'valid-api-key']);
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Starting API key verification');

        $this->apiClient->expects($this->once())
            ->method('verifyApiKey')
            ->with($this->context)
            ->willThrowException(new \RuntimeException('Unexpected error'));

        $this->logger->expects($this->once())
            ->method('critical')
            ->with(
                'Unexpected error during API key verification: Unexpected error',
                $this->arrayHasKey('trace')
            );

        $response = $this->controller->verifyApiKey($request, $this->context);
        
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals(
            ['success' => false, 'message' => 'An unexpected error occurred during verification'],
            json_decode($response->getContent(), true)
        );
    }
}
