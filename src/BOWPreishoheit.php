<?php declare(strict_types=1);

namespace BOW\Preishoheit;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BOWPreishoheit extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Create a FileLocator that knows where to look for config files
        $locator = new FileLocator('Resources/config');

        // Create a LoaderResolver that can handle YAML, glob patterns, and directories
        $resolver = new LoaderResolver([
            new YamlFileLoader($container, $locator),
            new GlobFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
        ]);

        // Wrap them into one DelegatingLoader that tries each loader in turn
        $configLoader = new DelegatingLoader($resolver);

        // Construct the path to "Resources/config" within our plugin
        $confDir = rtrim($this->getPath(), '/') . '/Resources/config';

        // Load all .yaml files from the "packages" subfolder in Resources/config
        // e.g. "Resources/config/packages/*.yaml"
        $configLoader->load($confDir . '/{packages}/*.yaml', 'glob');
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
