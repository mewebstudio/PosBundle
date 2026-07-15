<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\Gateway\ParamPos;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParamPosDefinitionBuilder extends AbstractGatewayDefinitionBuilder
{
    public function supports(string $gatewayClass): bool
    {
        return ParamPos::class === $gatewayClass;
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
                'user_name',
                'user_password',
                'secret_key',
            ]);
            $subResolver->setAllowedTypes('user_name', ['int', 'string']);
            $subResolver->setAllowedTypes('user_password', ['int', 'string']);
            $subResolver->setAllowedTypes('secret_key', ['int', 'string']);
            $subResolver->setDefined('terminal_id')
                ->setAllowedTypes('terminal_id', ['int', 'string']);
        });
    }
}
