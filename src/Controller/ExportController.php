<?php declare(strict_types=1);

namespace BOW\Preishoheit\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class ExportController extends AbstractController
{
    private EntityRepository $priceHistoryRepository;

    public function __construct(EntityRepository $priceHistoryRepository)
    {
        $this->priceHistoryRepository = $priceHistoryRepository;
    }

    /**
     * @Route("/api/_action/bow-preishoheit/export-history", name="api.action.bow.preishoheit.export.history", methods={"POST"})
     */
    public function exportHistory(Request $request, Context $context): Response
    {
        $criteria = new Criteria();
        $dateRange = $request->request->get('dateRange', []);

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

        $history = $this->priceHistoryRepository->search($criteria, $context);

        $csvData = [];
        $csvData[] = ['EAN/GTIN', 'Product Name', 'Old Price', 'New Price', 'Date/Time'];

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

        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="price-history.csv"');

        return $response;
    }
}
