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
            new Builder\AssecoPosDefinitionBuilder(),
            new Builder\GarantiPosDefinitionBuilder(),
            new Builder\InterPosDefinitionBuilder(),
            new Builder\KuveytPosDefinitionBuilder(),
            new Builder\VakifKatilimPosDefinitionBuilder(),
            new Builder\ParamPosDefinitionBuilder(),
            new Builder\Param3DHostPosDefinitionBuilder(),
            new Builder\PayFlexCPV4PosDefinitionBuilder(),
            new Builder\PayFlexV4PosDefinitionBuilder(),
            new Builder\PayForPosDefinitionBuilder(),
            new Builder\PosNetPosDefinitionBuilder(),
            new Builder\AkbankPosDefinitionBuilder(),
            new Builder\ToslaPosDefinitionBuilder(),
            new Builder\IyzicoPosDefinitionBuilder(),
            new Builder\PayTrPosDefinitionBuilder(),
        ];
    }

    /**
     * @param array<string, mixed> $options
     */
    public function createDefinition(string $name, array $options): Definition
    {
        foreach ($this->builders as $builder) {
            if ($builder->supports($options['gateway_class'])) {
                return $builder->createDefinition($name, $options);
            }
        }

        throw new \InvalidArgumentException(\sprintf('No builder found for gateway class "%s" (bank: "%s").', $options['gateway_class'], $name));
    }
}
