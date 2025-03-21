<?php declare(strict_types=1);

namespace Bow\Preishoheit;

use Bow\Preishoheit\ScheduledTask\CheckPreishoheitJobStatusTask;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskDefinition;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BOWPreishoheit extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        // Cleanup-Logik hier ergänzen falls notwendig
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        parent::deactivate($deactivateContext);
    }

    /**
     * Cronjob Interval dynamisch setzen über Definition
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->register(CheckPreishoheitJobStatusTask::class, ScheduledTaskDefinition::class)
            ->addTag('shopware.scheduled.task')
            ->addMethodCall('setDefaultInterval', [$this->getCronInterval()]);
    }

    private function getCronInterval(): int
    {
        // Default 5 Minuten
        return 300;
    }
}
