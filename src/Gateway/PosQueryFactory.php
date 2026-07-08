<?php

namespace Mews\PosBundle\Gateway;

use Mews\Pos\Factory\AccountFactory as MewsPosAccountFactory;
use Mews\Pos\Factory\PosQueryFactory as MewsPosPosQueryFactory;
use Mews\Pos\PosQuery\PosQueryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class PosQueryFactory
{
    public static function createPosQuery(
        string                   $name,
        array                    $options,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface          $logger,
        ClientInterface          $client
    ): PosQueryInterface {
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

        return MewsPosPosQueryFactory::create($account, $config, $eventDispatcher, $client, $logger);
    }
}
