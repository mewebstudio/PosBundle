<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\PayForPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayForPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return \in_array($gatewayClass, [PayForPos::class], true);
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
                ->setRequired('user_password')
                ->setAllowedTypes('user_password', ['int', 'string']);
        });
    }
}
