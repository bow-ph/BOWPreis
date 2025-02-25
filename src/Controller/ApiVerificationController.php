<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use BOW\Preishoheit\Exception\ApiVerificationException;
use Psr\Log\LoggerInterface;
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
    private LoggerInterface $logger;

    public function __construct(
        PreishoheitApiClient $apiClient,
        LoggerInterface $logger
    ) {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
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
        } catch (\Exception $e) {
            $this->logger->critical('Unexpected error during API key verification: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse([
                'success' => false,
                'message' => 'An unexpected error occurred during verification'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
