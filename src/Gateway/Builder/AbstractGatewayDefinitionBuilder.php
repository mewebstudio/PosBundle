<?php

namespace Mews\PosBundle\Gateway\Builder;

use Mews\Pos\PosInterface;
use Mews\PosBundle\Exception\MissingExtensionException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractGatewayDefinitionBuilder implements GatewayDefinitionBuilderInterface
{
    /**
     * @param array<string, mixed> $options
     */
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

    /** @return array<string, string> */
    abstract protected function getRequiredExtensions(): array;

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('gateway_class')
            ->setAllowedTypes('gateway_class', 'string');

        $this->setNestedOptions($resolver, 'gateway_endpoints', function (OptionsResolver $subResolver): void {
            $required = $this->getRequiredEndpoints();
            $optional = $this->getOptionalEndpoints();

            $subResolver->setRequired($required);
            if ($optional) {
                $subResolver->setDefined($optional);
            }
            foreach (\array_merge($required, $optional) as $key) {
                $subResolver->setAllowedTypes($key, 'string');
            }
        });

        $this->setNestedOptions($resolver, 'credentials', function (OptionsResolver $subResolver): void {
            $subResolver->setRequired([
                'merchant_id',
            ]);
            $subResolver->setAllowedTypes('merchant_id', ['int', 'string']);
        });

        $this->setNestedOptions($resolver, 'gateway_configs', function (OptionsResolver $subResolver): void {
            $subResolver->setDefault('test_mode', false)
                ->setAllowedTypes('test_mode', 'boolean');
            $subResolver->setDefault('disable_3d_hash_check', false)
                ->setAllowedTypes('disable_3d_hash_check', 'boolean');
            $subResolver->setDefault('lang', PosInterface::LANG_TR)
                ->setAllowedValues('lang', [PosInterface::LANG_TR, PosInterface::LANG_EN]);
        });
    }

    /** @return list<string> */
    protected function getRequiredEndpoints(): array
    {
        return ['payment_api'];
    }

    /** @return list<string> */
    protected function getOptionalEndpoints(): array
    {
        return ['gateway_3d_host'];
    }

    /**
     * Uses setOptions() (Symfony >=7.3) when available, falls back to setDefault() for older versions.
     * setDefault() with a nested OptionsResolver closure was removed in Symfony 8.0.
     */
    protected function setNestedOptions(OptionsResolver $resolver, string $name, \Closure $configurator): void
    {
        if (\method_exists($resolver, 'setOptions')) {
            $resolver->setOptions($name, $configurator);
        } else {
            $resolver->setDefault($name, $configurator);
        }
    }

    private function ensureRequiredExtensionsAvailable(string $name): void
    {
        $missingExtensions = [];
        foreach ($this->getRequiredExtensions() as $extensionName => $displayName) {
            if (!\extension_loaded($extensionName)) {
                $missingExtensions[] = $displayName;
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
