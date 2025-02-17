<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PreishoheitApiClient
{
    private const API_BASE_URL = 'https://pod.preishoheit.de/v2';
    private Client $client;
    private string $apiKey;

    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        $this->apiKey = $systemConfigService->getString('BOWPreishoheit.config.apiKey');
        $this->client = new Client([
            'base_uri' => self::API_BASE_URL,
            'headers' => [
                'Accept' => 'application/json',
                'X-API-Key' => $this->apiKey
            ]
        ]);
    }

    public function createJob(string $platform, array $identifiers): array
    {
        try {
            $response = $this->client->post('/jobs', [
                'form_params' => [
                    'platform' => $platform,
                    'data' => implode("\n", $identifiers)
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to create job: ' . $e->getMessage());
        }
    }

    public function getJobStatus(string $jobId): array
    {
        try {
            $response = $this->client->get("/jobs/{$jobId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to get job status: ' . $e->getMessage());
        }
    }

    public function downloadJobResult(string $jobId): array
    {
        try {
            $response = $this->client->get("/jobs/{$jobId}/download");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new PreishoheitApiException('Failed to download job result: ' . $e->getMessage());
        }
    }
}
