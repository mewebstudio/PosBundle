<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\AkbankPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AkbankPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    public function supports(string $gatewayClass): bool
    {
        return AkbankPos::class === $gatewayClass;
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
            $subResolver
                ->setRequired('terminal_id')
                ->setAllowedTypes('terminal_id', ['string', 'int']);
            $subResolver
                ->setRequired('secret_key')
                ->setAllowedTypes('secret_key', ['string', 'int']);
            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['string', 'int']);
        });
    }

    /** @return list<string> */
    protected function getRequiredEndpoints(): array
    {
        return \array_merge(parent::getRequiredEndpoints(), ['gateway_3d']);
    }
}
