<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\GarantiPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GarantiPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return \in_array($gatewayClass, [GarantiPos::class], true);
    }

    protected function getRequiredExtensions(): array
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('credentials', function (OptionsResolver $subResolver): void {
            $subResolver->setRequired([
                'user_name',
                'user_password',
                'terminal_id',
            ]);
            $subResolver->setAllowedTypes('user_name', ['int', 'string']);
            $subResolver->setAllowedTypes('user_password', ['int', 'string']);
            $subResolver->setAllowedTypes('terminal_id', ['int', 'string']);

            $subResolver->setDefined([
                'refund_user_name',
                'refund_user_password',
            ]);
            $subResolver->setAllowedTypes('refund_user_name', ['int', 'string']);
            $subResolver->setAllowedTypes('refund_user_password', ['int', 'string']);
        });

        $this->require3DGateway($resolver);
    }
}
