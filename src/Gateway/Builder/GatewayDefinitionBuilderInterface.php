<?php

namespace Mews\PosBundle\Gateway\Builder;

use Symfony\Component\DependencyInjection\Definition;

interface GatewayDefinitionBuilderInterface
{
    /**
     * @param class-string $gatewayClass
     *
     * @return bool
     */
    public function supports(string $gatewayClass): bool;

    /**
     * Create the definition for this gateway's builder given an array of options.
     */
    public function createDefinition(string $name, array $options): Definition;
}
