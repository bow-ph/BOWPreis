<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class ApiVerificationController extends AbstractController
{
    private PreishoheitApiClient $apiClient;

    public function __construct(PreishoheitApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @Route("/api/_action/bow-preishoheit/verify-api-key", name="api.action.bow.preishoheit.verify.api.key", methods={"POST"})
     */
    public function verifyApiKey(Request $request, Context $context): JsonResponse
    {
        try {
            $this->apiClient->verifyApiKey($context);
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
