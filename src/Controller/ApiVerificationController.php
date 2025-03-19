<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use BOW\Preishoheit\Exception\ApiVerificationException;
use BOW\Preishoheit\Service\Price\PriceAdjustmentService;
use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Attribute\Acl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Shopware\Core\Framework\Routing\Attribute\RouteScope;
use Symfony\Component\Routing\Attribute\Route;


#[Route(defaults: ['_routeScope' => ['api']], path: '/_action/bow-preishoheit')]
class ApiVerificationController extends AbstractController
{
    private PreishoheitApiClient $apiClient;
    private LoggerInterface $logger;
    private PriceAdjustmentService $priceAdjustmentService;
    private PriceUpdateService $priceUpdater;

    public function __construct(
        PreishoheitApiClient $apiClient,
        LoggerInterface $logger,
        PriceAdjustmentService $priceAdjustmentService,
        PriceUpdateService $priceUpdater
    ) {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
        $this->priceAdjustmentService = $priceAdjustmentService;
        $this->priceUpdater = $priceUpdater;
    }

    #[Route(path: '/verify-api-key', name: 'api.action.bow.preishoheit.verify.api.key', acl: 'bow_preishoheit.editor', methods: ['POST'])]
    public function verifyApiKey(Request $request, Context $context): JsonResponse
    {
        try {
            $this->logger->info('Starting API key verification');

            $apiKey = $request->request->get('apiKey');
            if (empty($apiKey)) {
                throw new ApiVerificationException('API key is required');
            }

            $this->apiClient->verifyApiKey($context);

            $this->logger->info('API key verification successful');
            return new JsonResponse(['success' => true]);
        } catch (ApiVerificationException $e) {
            $this->logger->error('API key verification failed: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error during API key verification', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            return new JsonResponse([
                'success' => false,
                'message' => 'An unexpected error occurred during verification'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
