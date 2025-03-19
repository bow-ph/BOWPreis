<?php declare(strict_types=1);

namespace BOW\Preishoheit\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LoggerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Ensure no services are trying to use monolog.logger directly
        foreach ($container->getDefinitions() as $id => $definition) {
            if (strpos($id, 'BOW\\Preishoheit\\') === 0) {
                $arguments = $definition->getArguments();
                foreach ($arguments as $key => $argument) {
                    if (is_string($argument) && $argument === 'monolog.logger') {
                        $definition->replaceArgument($key, null);
                    }
                }
            }
        }
    }
}
