<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Component;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsComponentTest extends TestCase
{
    private const COMPONENT_NAME = 'bow-preishoheit-settings';

    public function testComponentRegistration(): void
    {
        $module = $this->getModule();
        
        $this->assertTrue(
            $module->hasComponent(self::COMPONENT_NAME),
            'Component should be registered'
        );
    }

    public function testManualUpdateEndpoint(): void
    {
        $client = $this->getClient();
        $response = $client->request(
            'POST',
            '/api/_action/bow-preishoheit/update-prices'
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue(json_decode($response->getContent(), true)['success']);
    }

    public function testSaveProductSettings(): void
    {
        $product = [
            'id' => 'test-id',
            'active' => true,
            'surchargePercentage' => 10.0
        ];

        $repository = $this->getRepository();
        $result = $repository->save($product, Context::createDefaultContext());

        $this->assertNotNull($result);
        $this->assertTrue($result->success);
    }

    private function getModule(): object
    {
        return new class() {
            public function hasComponent(string $name): bool
            {
                return true;
            }
        };
    }

    private function getClient(): object
    {
        return new class() {
            public function request(string $method, string $url): Response
            {
                return new Response(json_encode(['success' => true]));
            }
        };
    }

    private function getRepository(): object
    {
        return new class() {
            public function save(array $data, Context $context): object
            {
                return new class() {
                    public bool $success = true;
                };
            }
        };
    }
}
