<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Routing\Attribute\Acl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class ExportController extends AbstractController
{
    private EntityRepository $priceHistoryRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityRepository $priceHistoryRepository,
        LoggerInterface $logger
    ) {
        $this->priceHistoryRepository = $priceHistoryRepository;
        $this->logger = $logger;
    }

    #[Route(path: "/api/_action/bow-preishoheit/export-history", name: "api.action.bow.preishoheit.export.history", acl: "bow_preishoheit.viewer", methods: ["POST"])]
    public function exportHistory(Request $request, Context $context): Response
    {
        $dateRange = $request->request->get('dateRange', []);

        $this->logger->info('Starting export of price history', ['dateRange' => $dateRange]);

        $criteria = new Criteria();

        if (!empty($dateRange['start'])) {
            $criteria->addFilter(new RangeFilter('createdAt', [
                RangeFilter::GTE => $dateRange['start']
            ]));
        }

        if (!empty($dateRange['end'])) {
            $criteria->addFilter(new RangeFilter('createdAt', [
                RangeFilter::LTE => $dateRange['end']
            ]));
        }

        try {
            $history = $this->priceHistoryRepository->search($criteria, $context);
            $csvData = [['EAN/GTIN', 'Product Name', 'Old Price', 'New Price', 'Date/Time']];

            foreach ($history->getElements() as $item) {
                $csvData[] = [
                    $item->getEan(),
                    $item->getProductName(),
                    $item->getOldPrice(),
                    $item->getNewPrice(),
                    $item->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            $csv = fopen('php://temp', 'r+');
            foreach ($csvData as $row) {
                fputcsv($csv, $row);
            }
            rewind($csv);
            $content = stream_get_contents($csv);
            fclose($csv);

            $this->logger->info('Price history export completed successfully', ['entries' => count($csvData) - 1]);

            $response = new Response($content);
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="price-history.csv"');

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error('Error during price history export', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);

            return new JsonResponse([
                'success' => false,
                'message' => 'An unexpected error occurred during the export.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
