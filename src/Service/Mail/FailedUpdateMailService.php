<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\Mail;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Content\Mail\Service\MailService;

class FailedUpdateMailService
{
    private EntityRepository $errorLogRepository;
    private MailService $mailService;
    private string $adminEmail;

    public function __construct(
        EntityRepository $errorLogRepository,
        MailService $mailService,
        string $adminEmail
    ) {
        $this->errorLogRepository = $errorLogRepository;
        $this->mailService = $mailService;
        $this->adminEmail = $adminEmail;
    }

    public function sendDailySummary(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new RangeFilter('createdAt', [
                RangeFilter::GTE => (new \DateTime())->modify('-24 hours')
            ])
        );

        $errors = $this->errorLogRepository->search($criteria, $context);
        if ($errors->count() === 0) {
            return;
        }

        $this->mailService->send([
            'subject' => 'BOW Preishoheit - Price Update Failures Summary',
            'contentHtml' => $this->createEmailContent($errors),
            'recipients' => [$this->adminEmail => 'Admin'],
            'senderName' => 'BOW Preishoheit'
        ], $context);
    }

    private function createEmailContent($errors): string
    {
        $content = '<h2>Price Update Failures in the Last 24 Hours</h2>';
        $content .= '<p>The following errors occurred during scheduled price updates:</p>';
        
        $content .= '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        $content .= '<tr style="background-color: #f5f5f5;">';
        $content .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Time</th>';
        $content .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Product</th>';
        $content .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Error</th>';
        $content .= '</tr>';

        foreach ($errors as $error) {
            $content .= sprintf(
                '<tr><td style="padding: 10px; border: 1px solid #ddd;">%s</td>' .
                '<td style="padding: 10px; border: 1px solid #ddd;">%s</td>' .
                '<td style="padding: 10px; border: 1px solid #ddd;">%s</td></tr>',
                $error->getCreatedAt()->format('Y-m-d H:i:s'),
                $error->getProductId(),
                htmlspecialchars($error->getMessage())
            );
        }

        $content .= '</table>';
        
        $content .= '<p style="margin-top: 20px;">Total errors: ' . $errors->count() . '</p>';
        $content .= '<p style="color: #666; font-size: 12px; margin-top: 30px;">This is an automated message from BOW Preishoheit plugin.</p>';
        
        return $content;
    }
}
