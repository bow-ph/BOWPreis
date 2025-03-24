<?php declare(strict_types=1);

namespace Bow\Preishoheit\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Psr\Log\LoggerInterface;

class PreishoheitApiService
{
    private Client $client;
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(SystemConfigService $configService, LoggerInterface $logger)
    {
        $endpoint = rtrim($configService->get('BowPreishoheit.config.apiEndpoint'), '/');
        $this->apiKey = $configService->get('BowPreishoheit.config.apiKey');
        
        // Base-URI auf "v2/jobs" festgelegt
        $this->client = new Client(['base_uri' => $endpoint . '/v2/jobs/']);
        $this->logger = $logger;
    }

    public function createJob(array $payload): array
    {
        try {
            $response = $this->client->post('', [
                'query' => ['api_key' => $this->apiKey],
                'json' => $payload
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $message = $e->getResponse() 
                ? $e->getResponse()->getBody()->getContents() 
                : $e->getMessage();

            $this->logger->error('Preishoheit API (createJob) Error', [
                'error' => $message,
                'payload' => $payload
            ]);

            throw new \RuntimeException("Preishoheit API (createJob) failed: {$message}");
        }
    }

    public function checkJobStatus(string $jobId): array
    {
        try {
            $response = $this->client->get("{$jobId}/status", [
                'query' => ['api_key' => $this->apiKey]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $message = $e->getResponse() 
                ? $e->getResponse()->getBody()->getContents() 
                : $e->getMessage();

            $this->logger->error('Preishoheit API (checkJobStatus) Error', [
                'error' => $message,
                'jobId' => $jobId
            ]);

            throw new \RuntimeException("Preishoheit API (checkJobStatus) failed: {$message}");
        }
    }
}
