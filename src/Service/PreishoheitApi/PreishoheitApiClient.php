<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;


use BOW\Preishoheit\Service\ErrorHandling\ErrorHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\Framework\Context;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PreishoheitApiClient
{
    private const API_BASE_URL = 'https://pod.preishoheit.de/v2';
    private Client $client;
    private string $apiKey;
    private ErrorHandler $errorHandler;

    public function __construct(
        SystemConfigService $systemConfigService,
        ErrorHandler $errorHandler
    ) {
        $this->apiKey = $systemConfigService->getString('BOWPreishoheit.config.apiKey');
        $this->errorHandler = $errorHandler;


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

    public function createJob(string $platform, array $identifiers, string $productId, Context $context): array

    {
        try {
            $response = $this->client->post('/jobs', [
                'form_params' => [
                    'platform' => $platform,
                    'data' => implode("\n", $identifiers)
                ]
            ]);


            $result = json_decode($response->getBody()->getContents(), true);
            
            if (!isset($result['job_id'])) {
                $this->errorHandler->handleError(
                    $productId,
                    'API_ERROR',
                    'No job ID received from API',
                    $context
                );
                throw new PreishoheitApiException('No job ID received from API');
            }

            return $result;
        } catch (GuzzleException $e) {
            $this->errorHandler->handleError(
                $productId,
                'API_ERROR',
                'Failed to create job: ' . $e->getMessage(),
                $context
            );

            throw new PreishoheitApiException('Failed to create job: ' . $e->getMessage());
        }
    }


    public function getJobStatus(string $jobId, string $productId, Context $context): array

    {
        try {
            $response = $this->client->get("/jobs/{$jobId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $this->errorHandler->handleError(
                $productId,
                'API_ERROR',
                'Failed to get job status: ' . $e->getMessage(),
                $context
            );

            throw new PreishoheitApiException('Failed to get job status: ' . $e->getMessage());
        }
    }

    public function downloadJobResult(string $jobId, string $productId, Context $context): array
    {
        try {
            $response = $this->client->get("/jobs/{$jobId}/download");
            $result = json_decode($response->getBody()->getContents(), true);
            
            if (empty($result['data'])) {
                $this->errorHandler->handleError(
                    $productId,
                    'API_ERROR',
                    'No data received from API',
                    $context
                );
                throw new PreishoheitApiException('No data received from API');
            }

            return $result;
        } catch (GuzzleException $e) {
            $this->errorHandler->handleError(
                $productId,
                'API_ERROR',
                'Failed to download job result: ' . $e->getMessage(),
                $context
            );

            throw new PreishoheitApiException('Failed to download job result: ' . $e->getMessage());
        }
    }
}
