<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use BOW\Preishoheit\Service\Price\PriceAdjustmentService;;
use BOW\Preishoheit\Service\PriceUpdateService;
use BOW\Preishoheit\Exception\ApiVerificationException;
use BOW\Preishoheit\Service\ErrorHandling\ErrorLogger;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\Acl;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api', 'administration']], path: '/api/_action/bow-preishoheit')]
class ApiVerificationController extends AbstractController
{
    private PreishoheitApiClient $apiClient;
    private ErrorLogger $errorLogger;
    private PriceAdjustmentService $priceAdjustmentService;
    private ProductPriceUpdater $priceUpdater;

    public function __construct(
        PreishoheitApiClient $apiClient,
        ErrorLogger $errorLogger,
        PriceAdjustmentService $priceAdjustmentService,
        ProductPriceUpdater $priceUpdater
    ) {
        $this->apiClient = $apiClient;
        $this->errorLogger = $errorLogger;
        $this->priceAdjustmentService = $priceAdjustmentService;
        $this->priceUpdater = $priceUpdater;
    }

    #[Route(path: '/verify-api-key', name: 'api.action.bow.preishoheit.verify.api.key', acl: 'bow_preishoheit.editor', methods: ['POST'])]
    public function verifyApiKey(Request $request, Context $context): JsonResponse
    {
        try {
            $this->errorLogger->info('Starting API key verification');

            $apiKey = $request->request->get('apiKey');
            if (empty($apiKey)) {
                throw new ApiVerificationException('API key is required');
            }

            $this->apiClient->verifyApiKey($context);

            $this->errorLogger->info('API key verification successful');
            return new JsonResponse(['success' => true]);
        } catch (ApiVerificationException $e) {
            $this->errorLogger->error('API key verification failed: ' . $e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->errorLogger->error('Unexpected error during API key verification: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse([
                'success' => false,
                'message' => 'An unexpected error occurred during verification'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: "/sync", name: "api.action.bow.preishoheit.sync", methods: ["POST"])]
    public function syncPrices(Request $request, Context $context): JsonResponse
    {
        try {
            // Example: Create job via API using new v2/jobs endpoint.
            $jobResponse = $this->apiClient->createJob('amazon', []);
            if (!isset($jobResponse['job_id'])) {
                throw new \Exception('No job ID received from API');
            }

            $jobResults = $this->apiClient->downloadJobResult($jobResponse['job_id']);

            foreach ($jobResults['data'] as $result) {
                // Example structure: ['productId' => string, 'basePrice' => float, 'surchargePercentage' => float]
                if (!isset($result['productId'], $result['basePrice'], $result['surchargePercentage'])) {
                    continue;
                }
                $newPrice = $this->priceAdjustmentService->calculateAdjustedPrice(
                    $result['basePrice'],
                    $result['surchargePercentage']
                );
                $this->priceUpdater->updatePrice($result['productId'], $newPrice, $context);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Synchronization complete'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->errorLogger->logSystemError($e->getMessage());
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
