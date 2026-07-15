<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\PayTrPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayTrPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    public function supports(string $gatewayClass): bool
    {
        return PayTrPos::class === $gatewayClass;
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
                'user_password',
                'secret_key',
            ]);
            $subResolver->setAllowedTypes('user_password', ['string']);
            $subResolver->setAllowedTypes('secret_key', ['string']);
        });
    }

    /** @return list<string> */
    protected function getRequiredEndpoints(): array
    {
        return \array_merge(parent::getRequiredEndpoints(), ['gateway_3d']);
    }
}
