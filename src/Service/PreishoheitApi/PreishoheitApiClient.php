<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
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

    public function verifyApiKey(): bool
    {
        try {
            $response = $this->getClient()->get('/verify-api-key');
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (isset($responseBody['header']['error'])) {
                throw new PreishoheitApiException($responseBody['header']['error']);
            }

            return true;
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to verify API key: ' . $e->getMessage());
        }
    }

    public function createJob(string $productGroup, array $identifiers, array $countries, array $categories): array
    {
        try {
            $payload = [
                'productGroup' => $productGroup,
                'identifiers' => $identifiers,
                'countries' => $countries,
                'categories' => $categories,
            ];

            $response = $this->getClient()->post('/jobs', [
                'json' => $payload
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (isset($responseBody['header']['error'])) {
                throw new PreishoheitApiException($responseBody['header']['error']);
            }

            return $responseBody['data'] ?? [];
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to create job: ' . $e->getMessage());
        }
    }

    public function getJobs(): array
    {
        try {
            $response = $this->getClient()->get('/jobs');
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (isset($responseBody['header']['error'])) {
                throw new PreishoheitApiException($responseBody['header']['error']);
            }

            return $responseBody['data'] ?? [];
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to fetch jobs: ' . $e->getMessage());
        }
    }

    public function closeJob(string $jobId): bool
    {
        try {
            $response = $this->getClient()->delete('/jobs/' . $jobId);
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to close job: ' . $e->getMessage());
        }
    }
}
