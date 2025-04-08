<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\PayFlexV4Pos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayFlexV4PosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return PayFlexV4Pos::class === $gatewayClass;
    }

    protected function getRequiredExtensions(): array
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $this->require3DGateway($resolver);

        $resolver->setDefault('gateway_endpoints', function (OptionsResolver $subResolver): void {
            $subResolver
                ->setRequired('query_api')
                ->setAllowedTypes('query_api', 'string');
        });

        $resolver->setDefault('credentials', function (OptionsResolver $subResolver): void {
            $subResolver->setRequired([
                'terminal_id',
                'user_password',
            ]);
            $subResolver->setAllowedTypes('terminal_id', ['int', 'string']);
            $subResolver->setAllowedTypes('user_password', ['int', 'string']);

            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['string']);

            // enc_key is not used, we set it to empty string by default.
            $subResolver->setDefault('enc_key', '');
        });
    }
}
