<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\AkbankPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AkbankPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return \in_array($gatewayClass, [AkbankPos::class], true);
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
                ->setRequired('terminal_id')
                ->setAllowedTypes('terminal_id', ['string', 'int']);
            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['string', 'int']);
        });

        $this->require3DGateway($resolver);
    }
}
