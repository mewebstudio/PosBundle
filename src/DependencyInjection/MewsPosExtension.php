<?php

namespace Mews\PosBundle\DependencyInjection;

use Mews\Pos\Factory\PosQueryFactory as MewsPosPosQueryFactory;
use Mews\Pos\PosInterface;
use Mews\Pos\PosQuery\PosQueryInterface;
use Mews\PosBundle\Gateway\GatewayDefinitionFactory;
use Mews\PosBundle\Gateway\GatewayFactory;
use Mews\PosBundle\Gateway\PosQueryFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MewsPosExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $definitionFactory = new GatewayDefinitionFactory();

        /** @var \Symfony\Component\Config\Definition\ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $i = 0;
        // Create gateway definition
        foreach ($config['banks'] as $bank => $bankConfigs) {
            $serviceId = $this->registerGateway($definitionFactory, $container, $bank, $bankConfigs);
            $queryServiceId = $this->registerGatewayQuery($container, $bank, $bankConfigs);

            if (0 === $i) {
                // set the first gateway as a default for injection
                $container->setAlias(PosInterface::class, $serviceId)
                    ->setPublic(false);
                if (null !== $queryServiceId) {
                    $container->setAlias(PosQueryInterface::class, $queryServiceId)
                        ->setPublic(false);
                }
                ++$i;
            }
        }
    }

    /**
     * @param array<string, mixed> $bankConfigs
     */
    private function registerGateway(GatewayDefinitionFactory $definitionFactory, ContainerBuilder $container, string $bank, array $bankConfigs): string
    {
        $gatewayDefinition = $definitionFactory->createDefinition($bank, $bankConfigs);
        $serviceId = 'mews_pos.gateway.' . $bank;

        $container->setDefinition($serviceId, $gatewayDefinition)
            ->setClass(PosInterface::class)
            ->setFactory([GatewayFactory::class, 'createPosGateway'])
            ->setArguments([
                '$name' => $bank,
                '$options' => $bankConfigs,
                '$eventDispatcher' => new Reference('event_dispatcher'),
                '$logger' => new Reference('logger'),
                '$client' => new Reference('psr18.http_client'),
            ])
            ->setPublic(false);

        $container->registerAliasForArgument($serviceId, PosInterface::class, $bank)
            ->setPublic(false);

        $gatewayDefinition->addTag('mews_pos.gateway', ['key' => $serviceId]);

        return $serviceId;
    }

    /**
     * @param array<string, mixed> $bankConfigs
     */
    private function registerGatewayQuery(ContainerBuilder $container, string $bank, array $bankConfigs): ?string
    {
        if (null === MewsPosPosQueryFactory::getPosQueryClassForGateway($bankConfigs['gateway_class'])) {
            return null;
        }

        $queryServiceId = 'mews_pos.query.' . $bank;
        $container->register($queryServiceId, PosQueryInterface::class)
            ->setFactory([PosQueryFactory::class, 'createPosQuery'])
            ->setArguments([
                '$name' => $bank,
                '$options' => $bankConfigs,
                '$eventDispatcher' => new Reference('event_dispatcher'),
                '$logger' => new Reference('logger'),
                '$client' => new Reference('psr18.http_client'),
            ])
            ->setPublic(false)
            ->addTag('mews_pos.query', ['key' => $queryServiceId]);

        $container->registerAliasForArgument($queryServiceId, PosQueryInterface::class, $bank)
            ->setPublic(false);

        return $queryServiceId;
    }
}
