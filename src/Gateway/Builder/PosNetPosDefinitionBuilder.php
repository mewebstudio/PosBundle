<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\PosNet;
use Mews\Pos\Gateways\PosNetV1Pos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PosNetPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return \in_array($gatewayClass, [PosNet::class, PosNetV1Pos::class], true);
    }

    protected function getRequiredExtensions(): array
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $this->require3DGateway($resolver);

        $resolver->setDefault('credentials', function (OptionsResolver $subResolver): void {

            $subResolver->setRequired([
                'terminal_id',
                'user_name' // required for 3D payment models
            ]);
            $subResolver->setAllowedTypes('terminal_id', ['int', 'string']);
            $subResolver->setAllowedTypes('user_name', ['int', 'string']);
        });
    }
}
