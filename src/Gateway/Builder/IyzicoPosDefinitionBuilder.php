<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\IyzicoPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IyzicoPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    public function supports(string $gatewayClass): bool
    {
        return IyzicoPos::class === $gatewayClass;
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
                ->setRequired('secret_key')
                ->setAllowedTypes('secret_key', ['string']);
            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['string']);
        });
    }

    /** @return list<string> */
    protected function getRequiredEndpoints(): array
    {
        return \array_merge(parent::getRequiredEndpoints(), ['query_api']);
    }
}
