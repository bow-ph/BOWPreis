<?php declare(strict_types=1);

namespace Bow\Preishoheit\Controller;

use Bow\Preishoheit\Service\MappingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class MappingController extends AbstractController
{
    public function __construct(private MappingService $mappingService) {}

    #[Route(path: '/api/bow-preishoheit/mapping', name: 'api.bow-preishoheit.mapping.get', methods: ['GET'])]
    public function getMappings(): JsonResponse
    {
        $mappings = $this->mappingService->getAllMappings();

        return new JsonResponse($mappings);
    }

    #[Route(path: '/api/bow-preishoheit/mapping', name: 'api.bow-preishoheit.mapping.create', methods: ['POST'])]
    public function createMapping(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if (empty($data['productId']) || empty($data['externalId'])) {
            return new JsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
        }

        $this->mappingService->createMapping($data['productId'], $data['externalId']);

        return new JsonResponse(['success' => true]);
    }

    #[Route(path: '/api/bow-preishoheit/mapping/{id}', name: 'api.bow-preishoheit.mapping.delete', methods: ['DELETE'])]
    public function deleteMapping(string $id): JsonResponse
    {
        $this->mappingService->deleteMapping($id);

        return new JsonResponse(['success' => true]);
    }
}
