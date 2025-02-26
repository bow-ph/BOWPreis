<?php declare(strict_types=1);

namespace BOW\Preishoheit;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use BOW\Preishoheit\DependencyInjection\TokenizerFixCompilerPass;

class BOWPreishoheit extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new TokenizerFixCompilerPass());
    }

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        // Check if the Tokenizer service exists before attempting to resolve the parameter
        if ($this->container->has('Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Tokenizer')) {
            // Force environment variable resolution before parent activation
            // This ensures the TOKEN_MINIMUM_LENGTH is properly resolved
            $this->container->getParameter('shopware.dbal.token_minimum_length');
        }

        parent::activate($activateContext);
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