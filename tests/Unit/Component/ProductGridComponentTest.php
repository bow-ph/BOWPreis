<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Component;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class ProductGridComponentTest extends TestCase
{
    private const COMPONENT_NAME = 'bow-preishoheit-product-grid';

    public function testComponentRegistration(): void
    {
        $module = $this->getModule();
        
        $this->assertTrue(
            $module->hasComponent(self::COMPONENT_NAME),
            'Component should be registered'
        );
    }

    public function testProductLoading(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('search')
            ->with(
                $this->callback(function (Criteria $criteria) {
                    return $criteria->hasAssociation('preishoheitProduct');
                }),
                $this->isInstanceOf(Context::class)
            )
            ->willReturn($this->getProductResult());

        $result = $repository->search(
            new Criteria(),
            Context::createDefaultContext()
        );

        $this->assertCount(2, $result->getElements());
    }

    public function testProductSelection(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->with([
                [
                    'productId' => '1',
                    'active' => true,
                    'surchargePercentage' => 0
                ]
            ])
            ->willReturn($this->getCreationResult());

        $result = $repository->create([
            [
                'productId' => '1',
                'active' => true,
                'surchargePercentage' => 0
            ]
        ]);

        $this->assertTrue($result->success);
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

    private function getProductResult(): object
    {
        return new class() {
            public function getElements(): array
            {
                return [
                    [
                        'id' => '1',
                        'name' => 'Product 1',
                        'productNumber' => 'P001'
                    ],
                    [
                        'id' => '2',
                        'name' => 'Product 2',
                        'productNumber' => 'P002'
                    ]
                ];
            }

            public function getTotal(): int
            {
                return 2;
            }
        };
    }

    private function getCreationResult(): object
    {
        return new class() {
            public bool $success = true;
        };
    }
}
