<?php declare(strict_types=1);

namespace Bow\Preishoheit\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PreishoheitApiService
{
    private Client $client;
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(SystemConfigService $configService)
    {
        $endpoint = $configService->get('BowPreishoheit.config.apiEndpoint');
        $this->apiKey = $configService->get('BowPreishoheit.config.apiKey');
        $this->client = new Client(['base_uri' => rtrim($endpoint, '/') . '/']);
        $this->logger = $logger;
    }

    public function createJob(array $payload): array
    {
        try {
            $response = $this->client->post('jobs', [
                'headers' => ['Authorization' => "Bearer {$this->apiKey}"],
                'json' => $payload
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            $message = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            throw new \RuntimeException("Preishoheit API (createJob) failed: {$message}");
        }
    }

    public function checkJobStatus(string $jobId): array
    {
        try {
            $response = $this->client->get("jobs/{$jobId}/status", [
                'headers' => ['Authorization' => "Bearer {$this->apiKey}"]
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            $message = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            throw new \RuntimeException("Preishoheit API (checkJobStatus) failed: {$message}");
        }
    }
}
