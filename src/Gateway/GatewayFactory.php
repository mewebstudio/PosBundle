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
    /**
     * @param non-empty-string $name
     * @param array{
     *     gateway_class: class-string<PosInterface>,
     *     credentials: array<non-empty-string, non-empty-string>,
     *     gateway_endpoints: array{payment_api: non-empty-string, query_api?: non-empty-string},
     *     gateway_configs?: array{lang?: 'en'|'tr', test_mode?: bool, disable_3d_hash_check?: bool}
     * } $options
     */
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
