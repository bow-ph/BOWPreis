<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Integration\Service\PreishoheitApi;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Symfony\Component\HttpFoundation\Response;

class PreishoheitApiClientTest extends TestCase
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

    public function testApiClientCreation(): void
    {
        $client = $this->getContainer()->get(PreishoheitApiClient::class);
        static::assertInstanceOf(PreishoheitApiClient::class, $client);
    }

    public function testCreateJob(): void
    {
        $client = $this->getContainer()->get(PreishoheitApiClient::class);
        $identifiers = ['1234567890'];

        $response = $client->createJob('amazon', $identifiers);

        static::assertArrayHasKey('job_id', $response);
        static::assertNotEmpty($response['job_id']);
    }

    public function testDownloadJobResult(): void
    {
        $client = $this->getContainer()->get(PreishoheitApiClient::class);
        $jobId = 'test-job-id';

        $result = $client->downloadJobResult($jobId);

        static::assertArrayHasKey('data', $result);
        static::assertIsArray($result['data']);
    }

    public function testVerifyApiKey(): void
    {
        $client = $this->getContainer()->get(PreishoheitApiClient::class);
        $result = $client->verifyApiKey($this->getContext());

        static::assertTrue($result);
    }
}
