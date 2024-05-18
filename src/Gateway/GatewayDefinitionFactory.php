<?php

namespace Mews\PosBundle\Gateway;

use Mews\PosBundle\Gateway\Builder\GatewayDefinitionBuilderInterface;
use Symfony\Component\DependencyInjection\Definition;

class GatewayDefinitionFactory
{
    /**
     * @var GatewayDefinitionBuilderInterface[]
     */
    private array $builders;

    public function __construct()
    {
        $this->builders = [
            new Builder\EstV3PosDefinitionBuilder(),
            new Builder\GarantiPosDefinitionBuilder(),
            new Builder\InterPosDefinitionBuilder(),
            new Builder\KuveytPosDefinitionBuilder(),
            new Builder\VakifKatilimPosDefinitionBuilder(),
            new Builder\PayFlexPosDefinitionBuilder(),
            new Builder\PayForPosDefinitionBuilder(),
            new Builder\PosNetPosDefinitionBuilder(),
            new Builder\AkbankPosDefinitionBuilder(),
            new Builder\ToslaPosDefinitionBuilder(),
        ];
    }

    public function createDefinition(string $name, array $options): ?Definition
    {
        foreach ($this->builders as $builder) {
            if ($builder->supports($options['gateway_class'])) {
                return $builder->createDefinition($name, $options);
            }
        }

        return null;
    }
}
