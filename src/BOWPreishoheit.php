<?php declare(strict_types=1);

namespace BOW\Preishoheit;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
// ADD THESE IMPORTS
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BOWPreishoheit extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Adjust the path if your monolog.yaml is in a different location.
        // For example: __DIR__ . '/Resources/config/packages' must point 
        // to the exact folder containing monolog.yaml
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config/packages'));
        $loader->load('monolog.yaml');
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
