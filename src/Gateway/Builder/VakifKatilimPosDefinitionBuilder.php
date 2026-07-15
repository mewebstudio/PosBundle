<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\VakifKatilimPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VakifKatilimPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    public function supports(string $gatewayClass): bool
    {
        return VakifKatilimPos::class === $gatewayClass;
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
                'user_name',
                'secret_key',
            ]);
            $subResolver->setAllowedTypes('terminal_id', ['int', 'string']);
            $subResolver->setAllowedTypes('user_name', ['int', 'string']);
            $subResolver->setAllowedTypes('secret_key', ['int', 'string']);
            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['int', 'string']);
        });
    }
}
