<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\ParamPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParamPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return \in_array($gatewayClass, [ParamPos::class], true);
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
            ]);
            $subResolver->setAllowedTypes('user_name', ['int', 'string']);
            $subResolver->setAllowedTypes('user_password', ['int', 'string']);
        });

    }

    protected function getOptionalEndpoints(): array
    {
        return \array_merge(parent::getOptionalEndpoints(), ['payment_api_2']);
    }
}
