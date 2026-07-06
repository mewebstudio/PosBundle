<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\PayForPos;
use Mews\Pos\Model\Account\PayForPosAccount;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayForPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    public function supports(string $gatewayClass): bool
    {
        return PayForPos::class === $gatewayClass;
    }

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
                ->setRequired('user_password')
                ->setAllowedTypes('user_password', ['int', 'string']);
            $subResolver->setDefined('secret_key')
                ->setAllowedTypes('secret_key', ['int', 'string']);
            $subResolver
                ->setDefined('mbr_id')
                ->setDefault('mbr_id', PayForPosAccount::MBR_ID_FINANSBANK)
                ->setAllowedTypes('mbr_id', ['int', 'string']);
        });
    }

    protected function getRequiredEndpoints(): array
    {
        return \array_merge(parent::getRequiredEndpoints(), ['gateway_3d']);
    }
}
