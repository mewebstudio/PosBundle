<?php

namespace Mews\PosBundle\Gateway;

use Mews\Pos\Factory\AccountFactory as MewsPosAccountFactory;
use Mews\Pos\Factory\PosFactory;
use Mews\Pos\PosInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class GatewayFactory
{
    public static function createPosGateway(
        string                   $name,
        array                    $options,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface          $logger,
        ClientInterface          $client
    ): PosInterface {
        $account = MewsPosAccountFactory::createForGateway(
            $options['gateway_class'],
            $name,
            $options['credentials']
        );

        $config = [
            'class'             => $options['gateway_class'],
            'gateway_endpoints' => $options['gateway_endpoints'],
            'gateway_configs'   => $options['gateway_configs'] ?? [],
        ];

        return PosFactory::create(
            $account,
            $config,
            $eventDispatcher,
            null,
            $client,
            $logger
        );
    }
}
