<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\ToslaPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToslaPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return \in_array($gatewayClass, [ToslaPos::class], true);
    }

    protected function getRequiredExtensions(): array
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('credentials', function (OptionsResolver $subResolver): void {
            $subResolver
                ->setRequired('user_name')
                ->setAllowedTypes('user_name', ['int', 'string']);
            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['int', 'string']);
        });
    }
}
