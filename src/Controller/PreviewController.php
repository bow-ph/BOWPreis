<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class PreviewController extends AbstractController
{
    private PreishoheitApiClient $apiClient;
    private SystemConfigService $configService;
    private LoggerInterface $logger;

    public function __construct(
        PreishoheitApiClient $apiClient,
        SystemConfigService $configService,
        LoggerInterface $logger
    ) {
        $this->apiClient = $apiClient;
        $this->configService = $configService;
        $this->logger = $logger;
    }

    #[Route(path: '/api/_action/bow-preishoheit/preview', name: 'api.action.bow.preishoheit.preview', methods: ['POST'])]
    public function getPreviewData(Request $request): JsonResponse
    {
        try {
            $config = $request->request->all();

            $productGroup = $config['productGroup'] ?? $this->configService->get('BOWPreishoheit.config.productGroup', 'amazon');
            $countries = $config['countries'] ?? $this->configService->get('BOWPreishoheit.config.countrySelection', ['de']);
            $identifiers = $config['identifiers'] ?? [];

            $responseData = $this->apiClient->createJob($productGroup, $identifiers, $countries);

            return new JsonResponse([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('Preview data fetch failed', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);

            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
