<?php declare(strict_types=1);

namespace Bow\Preishoheit\Controller;

use Bow\Preishoheit\Service\PreishoheitApiService;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class ConfigController extends AbstractController
{
    public function __construct(private SystemConfigService $systemConfigService) {}

    #[Route(path: '/api/bow-preishoheit/config', name: 'api.bow-preishoheit.config.get', methods: ['GET'])]
    public function getConfig(): JsonResponse
    {
        $config = [
            'apiKey' => $this->systemConfigService->get('BowPreishoheit.config.apiKey'),
            'apiEndpoint' => $this->systemConfigService->get('BowPreishoheit.config.apiEndpoint'),
        ];

        return new JsonResponse(['config' => $config]);
    }

    #[Route(path: '/api/bow-preishoheit/config', name: 'api.bow-preishoheit.config.update', methods: ['POST'])]
    public function updateConfig(Request $request): JsonResponse
    {
        $configData = $request->request->all();

        if (isset($configData['apiKey'])) {
            $this->systemConfigService->set('BowPreishoheit.config.apiKey', $configData['apiKey']);
        }

        if (isset($configData['apiEndpoint'])) {
            $this->systemConfigService->set('BowPreishoheit.config.apiEndpoint',
        }
    }
}