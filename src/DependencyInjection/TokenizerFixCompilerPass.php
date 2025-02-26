<?php declare(strict_types=1);

namespace BOW\Preishoheit\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TokenizerFixCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Tokenizer')) {
            return;
        }

        $definition = $container->getDefinition('Shopware\Core\Framework\DataAbstractionLayer\Search\Term\Tokenizer');
        $tokenMinimumLength = $container->getParameter('shopware.dbal.token_minimum_length');

        $definition->setArguments([(int) $tokenMinimumLength]);
    }
}