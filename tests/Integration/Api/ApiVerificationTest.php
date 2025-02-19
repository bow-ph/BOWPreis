<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Integration\Api;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Symfony\Component\HttpFoundation\Response;

class ApiVerificationTest extends TestCase
{
    use IntegrationTestBehaviour;

    private const TEST_API_KEY = 'test-api-key';

    protected function setUp(): void
    {
        parent::setUp();
        $this->getContainer()->get('system_config.repository')
            ->update([[
                'configurationKey' => 'BOWPreishoheit.config.apiKey',
                'configurationValue' => self::TEST_API_KEY
            ]], $this->getContext());
    }

    public function testApiKeyVerificationEndpoint(): void
    {
        $browser = $this->getBrowser();
        $response = $browser->request(
            'POST',
            '/api/_action/bow-preishoheit/verify-api-key',
            ['apiKey' => self::TEST_API_KEY]
        );

        static::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        static::assertTrue(json_decode($response->getContent(), true)['success']);
    }

    public function testApiKeyVerificationWithInvalidKey(): void
    {
        $browser = $this->getBrowser();
        $response = $browser->request(
            'POST',
            '/api/_action/bow-preishoheit/verify-api-key',
            ['apiKey' => 'invalid-key']
        );

        static::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        static::assertFalse(json_decode($response->getContent(), true)['success']);
    }

    public function testApiKeyVerificationWithMissingKey(): void
    {
        $browser = $this->getBrowser();
        $response = $browser->request(
            'POST',
            '/api/_action/bow-preishoheit/verify-api-key',
            []
        );

        static::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        static::assertFalse(json_decode($response->getContent(), true)['success']);
    }

    public function testConfigurationPersistence(): void
    {
        $systemConfigService = $this->getContainer()->get('Shopware\Core\System\SystemConfig\SystemConfigService');
        $value = $systemConfigService->get('BOWPreishoheit.config.apiKey');
        
        static::assertEquals(self::TEST_API_KEY, $value);
    }

    public function testApiClientIntegration(): void
    {
        $apiClient = $this->getContainer()->get(PreishoheitApiClient::class);
        
        try {
            $apiClient->verifyApiKey($this->getContext());
            static::assertTrue(true);
        } catch (\Exception $e) {
            static::fail('API client integration failed: ' . $e->getMessage());
        }
    }
}
