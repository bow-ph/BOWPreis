<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PreishoheitApiClient
{
    private const API_BASE_URL = 'https://pod.preishoheit.de/v2';
    private Client $client;
    private string $apiKey;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->apiKey = $systemConfigService->getString('BOWPreishoheit.config.apiKey');
        $this->client = new Client([
            'base_uri' => self::API_BASE_URL,
            'headers' => [
                'Accept' => 'application/json',
                'X-API-Key' => $this->apiKey
            ]
        ]);
    }

    public function verifyApiKey(Context $context): bool
    {
        try {
            $response = $this->client->get('/jobs');
            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['header']['status']) && $result['header']['status'] === 'fail') {
                throw new PreishoheitApiException($result['header']['error'] ?? 'API verification failed');
            }

            return true;
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to verify API key: ' . $e->getMessage());
        }
    }

    // Previous methods remain unchanged
}
