<?php

declare(strict_types=1);

namespace Mews\PosBundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PosBundleCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        //
    }
}
