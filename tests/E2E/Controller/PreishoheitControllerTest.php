<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\E2E\Controller;

use Shopware\Core\Framework\Test\TestCaseBase\AdminApiTestBehaviour;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;

class PreishoheitControllerTest extends TestCase
{
    use IntegrationTestBehaviour;
    use AdminApiTestBehaviour;

    public function testAdminModuleLoads(): void
    {
        $this->markTestIncomplete('E2E test to be implemented');
    }
}
