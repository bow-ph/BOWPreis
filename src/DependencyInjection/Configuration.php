<?php declare(strict_types=1);

namespace BOW\Preishoheit\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bow_preishoheit');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
