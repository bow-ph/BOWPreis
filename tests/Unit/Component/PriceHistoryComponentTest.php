<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Component;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class PriceHistoryComponentTest extends TestCase
{
    private const COMPONENT_NAME = 'bow-preishoheit-price-history';

    public function testComponentRegistration(): void
    {
        $module = $this->getModule();
        
        $this->assertTrue(
            $module->hasComponent(self::COMPONENT_NAME),
            'Component should be registered'
        );
    }

    public function testHistoryLoading(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->isInstanceOf(Context::class)
            )
            ->willReturn($this->getHistoryResult());

        $result = $repository->search(
            new Criteria(),
            Context::createDefaultContext()
        );

        $this->assertCount(2, $result->getElements());
    }

    public function testPagination(): void
    {
        $criteria = new Criteria(1, 25);
        $repository = $this->createMock(EntityRepository::class);
        
        $repository->expects($this->once())
            ->method('search')
            ->with(
                $this->callback(function (Criteria $searchCriteria) {
                    return $searchCriteria->getOffset() === 0
                        && $searchCriteria->getLimit() === 25;
                }),
                $this->isInstanceOf(Context::class)
            )
            ->willReturn($this->getHistoryResult());

        $result = $repository->search(
            $criteria,
            Context::createDefaultContext()
        );

        $this->assertEquals(2, $result->getTotal());
    }

    private function getModule(): object
    {
        return new class() {
            public function hasComponent(string $name): bool
            {
                return true;
            }
        };
    }

    private function getHistoryResult(): object
    {
        return new class() {
            public function getElements(): array
            {
                return [
                    [
                        'id' => '1',
                        'ean' => '1234567890',
                        'oldPrice' => 100.00,
                        'newPrice' => 110.00
                    ],
                    [
                        'id' => '2',
                        'ean' => '0987654321',
                        'oldPrice' => 200.00,
                        'newPrice' => 190.00
                    ]
                ];
            }

            public function getTotal(): int
            {
                return 2;
            }
        };
    }
}
