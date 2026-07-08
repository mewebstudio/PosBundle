<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\PayFlexCPV4Pos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayFlexCPV4PosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return PayFlexCPV4Pos::class === $gatewayClass;
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
                'terminal_id',
                'user_password',
            ]);
            $subResolver->setAllowedTypes('terminal_id', ['int', 'string']);
            $subResolver->setAllowedTypes('user_password', ['int', 'string']);

            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['string']);
        });
    }
}
