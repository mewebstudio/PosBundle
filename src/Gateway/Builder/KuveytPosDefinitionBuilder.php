<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateways\KuveytPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KuveytPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return \in_array($gatewayClass, [KuveytPos::class], true);
    }

    protected function getRequiredExtensions(): array
    {
        return [
            'soap' => 'ext-soap',
        ];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $this->setNestedOptions($resolver, 'credentials', function (OptionsResolver $subResolver): void {
            $subResolver->setRequired([
                'user_name',
                'terminal_id',
            ]);
            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['int', 'string']);
        });
    }

    protected function getRequiredEndpoints(): array
    {
        return \array_merge(parent::getRequiredEndpoints(), ['gateway_3d', 'query_api']);
    }
}
