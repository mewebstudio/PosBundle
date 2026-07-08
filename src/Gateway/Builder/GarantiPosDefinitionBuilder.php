<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\GarantiPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GarantiPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    public function supports(string $gatewayClass): bool
    {
        return GarantiPos::class === $gatewayClass;
    }

    /** @return array<string, string> */
    protected function getRequiredExtensions(): array
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $this->setNestedOptions($resolver, 'credentials', function (OptionsResolver $subResolver): void {
            $subResolver->setRequired([
                'user_name',
                'user_password',
                'terminal_id',
            ]);
            $subResolver->setAllowedTypes('user_name', ['int', 'string']);
            $subResolver->setAllowedTypes('user_password', ['int', 'string']);
            $subResolver->setAllowedTypes('terminal_id', ['int', 'string']);

            $subResolver->setDefined('secret_key')
                ->setAllowedTypes('secret_key', ['int', 'string']);
            $subResolver->setDefined([
                'refund_user_name',
                'refund_user_password',
            ]);
            $subResolver->setAllowedTypes('refund_user_name', ['int', 'string']);
            $subResolver->setAllowedTypes('refund_user_password', ['int', 'string']);
        });
    }

    /** @return list<string> */
    protected function getRequiredEndpoints(): array
    {
        return \array_merge(parent::getRequiredEndpoints(), ['gateway_3d']);
    }
}
