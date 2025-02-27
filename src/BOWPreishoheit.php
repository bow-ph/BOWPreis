<?php declare(strict_types=1);

namespace BOW\Preishoheit;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use BOW\Preishoheit\DependencyInjection\BOWPreishoheitExtension;

class BOWPreishoheit extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->registerExtension(new BOWPreishoheitExtension());
    }

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

        // Clean up code can go here
    }
}
