<?php

namespace Mews\PosBundle\DependencyInjection;

use Mews\Pos\PosInterface;
use Mews\PosBundle\Gateway\GatewayDefinitionFactory;
use Mews\PosBundle\Gateway\GatewayFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MewsPosExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $definitionFactory = new GatewayDefinitionFactory();

        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);

        $i = 0;
        // Create gateway definition
        foreach ($config['banks'] as $bank => $bankConfigs) {

            if ($gatewayDefinition = $definitionFactory->createDefinition($bank, $bankConfigs)) {
                $serviceId = 'mews_pos.gateway.'.$bank;

                $container->setDefinition($serviceId, $gatewayDefinition)
                    ->setClass(PosInterface::class)
                    ->setFactory([GatewayFactory::class, 'createPosGateway'])
                    ->setArguments([
                        '$name'            => $bank,
                        '$options'         => $bankConfigs,
                        '$eventDispatcher' => new Reference('event_dispatcher'),
                        '$logger'          => new Reference('logger'),
                        '$client'          => new Reference('psr18.http_client'),
                    ])
                    ->setPublic(false);

                $container->registerAliasForArgument($serviceId, PosInterface::class, $bank)
                    ->setPublic(false);

                $gatewayDefinition->addTag('mews_pos.gateway', ['key' => $serviceId]);

                if ($i === 0) {
                    // set the first gateway as a default for injection
                    $container->setAlias(PosInterface::class, $serviceId)
                        ->setPublic(false);
                    $i++;
                }
            }
        }
    }
}
