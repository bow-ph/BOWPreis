<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use BOW\Preishoheit\Service\ErrorHandling\ErrorLogger;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PreishoheitApiClient
{
    private const API_BASE_URL = 'https://pod.preishoheit.de/v2';
    private ?Client $client = null;
    private SystemConfigService $systemConfigService;
    private ErrorLogger $errorLogger;

    public function __construct(SystemConfigService $systemConfigService, ErrorLogger $errorLogger)
    {
        $this->systemConfigService = $systemConfigService;
        $this->errorLogger = $errorLogger;
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $apiKey = $this->systemConfigService->getString('BOWPreishoheit.config.apiKey');
            if (empty($apiKey)) {
                throw new PreishoheitApiException('API key is required');
            }
            
            $this->client = new Client([
                'base_uri' => self::API_BASE_URL,
                'headers' => [
                    'Accept' => 'application/json',
                    'X-API-Key' => $apiKey
                ]
            ]);
        }

        return $this->client;
    }

    public function verifyApiKey(Context $context): bool
    {
        try {
            $response = $this->getClient()->get('/jobs');
            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['header']['status']) && $result['header']['status'] === 'fail') {
                throw new PreishoheitApiException($result['header']['error'] ?? 'API verification failed');
            }

            return true;
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to verify API key: ' . $e->getMessage());
        }
    }
}
