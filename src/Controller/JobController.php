<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use BOW\Preishoheit\Exception\PreishoheitApiException;

#[Route(defaults: ['_routeScope' => ['api']])]
class JobController extends AbstractController
{
    private PreishoheitApiClient $apiClient;
    private LoggerInterface $logger;

    public function __construct(PreishoheitApiClient $apiClient, LoggerInterface $logger)
    {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
    }

    #[Route(path: '/api/bow-preishoheit/jobs', name: 'api.bow.preishoheit.jobs', methods: ['GET'])]
    public function listJobs(): JsonResponse
    {
        try {
            $jobs = $this->apiClient->getJobs();

            return new JsonResponse(['success' => true, 'data' => $jobs]);
        } catch (PreishoheitApiException $e) {
            $this->logger->error('Error fetching jobs', ['exception' => $e]);

            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/api/_action/bow-preishoheit/jobs/create', name: 'api.bow.preishoheit.jobs.create', methods: ['POST'])]
public function createJob(Request $request, Context $context): JsonResponse
{
    $data = $request->request->all();

    try {
        $response = $this->apiClient->createJob(
            $data['productGroup'],
            $data['identifiers'],
            $data['countries'],
            $data['categories'] ?? [],
            $data['dynamicProductGroupId'] ?? null
        );

        return new JsonResponse([
            'success' => true,
            'data' => $response
        ]);
    } catch (\Throwable $e) {
        $this->logger->error('Error creating job', [
            'message' => $e->getMessage(),
            'exception' => $e
        ]);

        return new JsonResponse([
            'success' => false,
            'message' => 'Failed to create job',
            'error' => $e->getMessage()
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
