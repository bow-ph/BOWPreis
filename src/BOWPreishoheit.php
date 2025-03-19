<?php declare(strict_types=1);

namespace BOW\Preishoheit;

use BOW\Preishoheit\DependencyInjection\LoggerCompilerPass;
use BOW\Preishoheit\DependencyInjection\TokenizerFixCompilerPass;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class BOWPreishoheit extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new TokenizerFixCompilerPass());
        $container->addCompilerPass(new LoggerCompilerPass());
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
