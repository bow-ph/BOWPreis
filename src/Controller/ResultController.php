<?php declare(strict_types=1);

namespace Bow\Preishoheit\Controller;

use Bow\Preishoheit\Service\ResultMappingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route(defaults: ['_routeScope' => ['api']])]
class ResultController extends AbstractController
{
    public function __construct(private ResultMappingService $resultService) {}

    #[Route(path: '/api/bow-preishoheit/results', name: 'api.bow-preishoheit.results.list', methods: ['GET'])]
    public function listResults(): JsonResponse
    {
        $results = $this->resultService->getAllResults();

        return new JsonResponse(['data' => $results]);
    }

    #[Route(path: '/api/bow-preishoheit/results/{jobId}', name: 'api.bow-preishoheit.results.detail', methods: ['GET'])]
    public function resultDetail(string $jobId): JsonResponse
    {
        $result = $this->resultService->getResultByJobId($jobId);

        return new JsonResponse(['data' => $result]);
    }

    #[Route(path: '/api/bow-preishoheit/results/{jobId}', name: 'api.bow-preishoheit.results.update', methods: ['POST'])]
    public function updateResult(string $jobId, Request $request): JsonResponse
    {
        $data = $request->toArray();

        $this->resultService->updateResult($jobId, $data);

        return new JsonResponse(['success' => true]);
    }

    #[Route(path: '/api/bow-preishoheit/results/save-approved', methods: ['POST'])]
public function saveApprovedResults(
    Request $request,
    ResultMappingService $resultService
): JsonResponse {
    $approvedResults = $request->toArray();
    $resultService->saveApprovedResults($approvedResults);

    return new JsonResponse(['success' => true]);
}

}
