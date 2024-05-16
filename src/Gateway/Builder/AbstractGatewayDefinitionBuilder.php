<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\PosInterface;
use Mews\PosBundle\Exception\MissingExtensionException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractGatewayDefinitionBuilder implements GatewayDefinitionBuilderInterface
{
    final public function createDefinition(string $name, array $options): Definition
    {
        $this->ensureRequiredExtensionsAvailable($name);

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $resolver->resolve($options);

        $definition = new Definition();
        $definition->setPublic(false);

        return $definition;
    }

    abstract protected function getRequiredExtensions(): array;

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'lang'      => PosInterface::LANG_TR,
            'test_mode' => false,
        ]);
        $resolver->setAllowedValues('lang', [
            PosInterface::LANG_TR,
            PosInterface::LANG_EN,
        ]);
        $resolver->setAllowedTypes('test_mode', 'boolean');

        $resolver
            ->setRequired('gateway_class')
            ->setAllowedTypes('gateway_class', 'string');

        $resolver->setDefault('gateway_endpoints', function (OptionsResolver $subResolver): void {
            $subResolver->setDefined([
                'gateway_3d_host',
            ]);
            $subResolver->setRequired([
                'payment_api',
                'gateway_3d',
            ]);
            $subResolver->setAllowedTypes('payment_api', ['string']);
            $subResolver->setAllowedTypes('gateway_3d', ['string']);
            $subResolver->setAllowedTypes('gateway_3d_host', ['string']);
        });

        $resolver->setDefault('credentials', function (OptionsResolver $subResolver): void {
            $subResolver->setRequired([
                'merchant_id',
                'payment_model',
                'enc_key', // for 3D models only
            ]);
            $subResolver->setAllowedTypes('merchant_id', ['int', 'string']);
            $subResolver->setAllowedTypes('enc_key', ['int', 'string']);
            $subResolver->setAllowedValues('payment_model', [
                PosInterface::MODEL_NON_SECURE,
                PosInterface::MODEL_3D_SECURE,
                PosInterface::MODEL_3D_PAY,
                PosInterface::MODEL_3D_PAY_HOSTING,
                PosInterface::MODEL_3D_HOST,
            ]);
        });
    }

    private function ensureRequiredExtensionsAvailable(string $name): void
    {
        $missingExtensions = [];
        foreach ($this->getRequiredExtensions() as $requiredClass => $extension) {
            if (!\class_exists($requiredClass)) {
                $missingExtensions[] = $extension;
            }
        }

        if ([] === $missingExtensions) {
            return;
        }

        throw new MissingExtensionException(\sprintf(
            "Missing PHP extension%s, to use the \"%s\" adapter, please install %s",
            \count($missingExtensions) > 1 ? 's' : '',
            $name,
            \implode(' ', $missingExtensions)
        ));
    }
}
