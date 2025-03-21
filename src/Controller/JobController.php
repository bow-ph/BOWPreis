<?php declare(strict_types=1);

namespace Bow\Preishoheit\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route(defaults: ['_routeScope' => ['api']])]
class JobController extends AbstractController
{
    #[Route('/api/bow-preishoheit/job', methods: ['POST'])]
    public function createJob(Request $request, JobService $jobService, PreishoheitApiService $apiService): JsonResponse
    {
        $data = $request->toArray();
    
        $productIds = $data['products'] ?? [];
    
        foreach ($productIds as $productId) {
            $jobId = $jobService->createJob($productId);
    
            $apiPayload = [
                'ean' => $data['ean'],
                'country' => $data['country'],
                'platform' => $data['platform'],
                'category' => $data['category'],
                'productId' => $productId
            ];
    
            $apiResponse = $apiService->createJob($apiPayload);
    
            $jobService->updateJobStatus($jobId, $apiResponse['status']);
        }
    
        return new JsonResponse(['success' => true]);
    }

    #[Route(path: '/api/bow-preishoheit/job/{id}', name: 'api.bow-preishoheit.job.detail', methods: ['GET'])]
    public function getJob(string $id): JsonResponse
    {
        return new JsonResponse(['jobId' => $id]);
    }
}