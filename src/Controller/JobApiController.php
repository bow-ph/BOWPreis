<?php declare(strict_types=1);

namespace Bow\Preishoheit\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route(defaults: ['_routeScope' => ['api']])]
class JobApiController extends AbstractController
{
    #[Route(path: '/api/bow-preishoheit/job-api', name: 'api.bow-preishoheit.job-api.create', methods: ['POST'])]
    public function createExternalJob(Request $request): JsonResponse
    {
        return new JsonResponse(['jobCreated' => true]);
    }

    #[Route(path: '/api/bow-preishoheit/job-api/status/{jobId}', name: 'api.bow-preishoheit.job-api.status', methods: ['GET'])]
    public function getJobStatus(string $jobId): JsonResponse
    {
        return new JsonResponse(['jobId' => $jobId, 'status' => 'pending']);
    }
}
