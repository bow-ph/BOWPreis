<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use BOW\Preishoheit\Exception\PreishoheitApiException;

class PreishoheitApiClient
{
    private const API_BASE_URL = 'https://pod.preishoheit.de/v2';

    private ?Client $client = null;
    private SystemConfigService $systemConfigService;
    private LoggerInterface $logger;

    public function __construct(
        SystemConfigService $systemConfigService,
        LoggerInterface $logger
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $apiKey = $this->systemConfigService->get('BOWPreishoheit.config.apiKey');

            if (empty($apiKey)) {
                $this->logger->error('API key is missing in configuration');
                throw new PreishoheitApiException('API key is missing');
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
            $response = $this->getClient()->get('/verify-api-key');
            if ($response->getStatusCode() !== 200) {
                $this->logger->error('API responded with invalid status code', [
                    'statusCode' => $response->getStatusCode()
                ]);
                throw new PreishoheitApiException('Invalid API response status: ' . $response->getStatusCode());
            }
    
            $responseBody = json_decode($response->getBody()->getContents(), true);
    
            if (isset($responseBody['header']['error'])) {
                $errorBody = $responseBody['header']['error'];
                $this->logger->error('API key verification failed: ' . $errorBody);
                throw new PreishoheitApiException($errorBody);
            }
    
            $this->logger->info('API key verification succeeded.');
            return true;
        } catch (GuzzleException $e) {
            $this->logger->error('HTTP Error verifying API key', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
    
            throw new PreishoheitApiException('Failed to verify API key: ' . $e->getMessage());
        }
    }

    public function createJob(string $productGroup, array $identifiers, array $countries): array
{
    try {
        $payload = [
            'productGroup' => $productGroup,
            'identifiers' => $identifiers,
            'countries' => $countries,
        ];

        $response = $this->getClient()->post('/jobs', [
            'json' => $payload
        ]);

        if ($response->getStatusCode() !== 200) {
            $this->logger->error('Job creation failed with invalid status code', [
                'statusCode' => $response->getStatusCode()
            ]);

            throw new PreishoheitApiException('Invalid API response status: ' . $response->getStatusCode());
        }

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if (isset($responseBody['header']['error'])) {
            $errorBody = $responseBody['header']['error'];
            $this->logger->error('Job creation error: ' . $errorBody);

            throw new PreishoheitApiException($errorBody);
        }

        return $responseBody['data'] ?? [];
    } catch (GuzzleException $e) {
        $this->logger->error('HTTP Error creating job', [
            'message' => $e->getMessage(),
            'exception' => $e
        ]);

        throw new PreishoheitApiException('Failed to create job: ' . $e->getMessage());
    }
}

    
}
