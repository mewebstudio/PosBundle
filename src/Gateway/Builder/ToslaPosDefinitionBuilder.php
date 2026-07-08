<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\ToslaPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToslaPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    /**
     * @inheritDoc
     */
    public function supports(string $gatewayClass): bool
    {
        return ToslaPos::class === $gatewayClass;
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
                ->setRequired('user_name')
                ->setAllowedTypes('user_name', ['int', 'string']);
            $subResolver
                ->setRequired('secret_key')
                ->setAllowedTypes('secret_key', ['int', 'string']);
            $subResolver
                ->setDefined('sub_merchant_id')
                ->setAllowedTypes('sub_merchant_id', ['int', 'string']);
        });
    }

    /** @return list<string> */
    protected function getRequiredEndpoints(): array
    {
        return \array_merge(parent::getRequiredEndpoints(), ['gateway_3d']);
    }
}
