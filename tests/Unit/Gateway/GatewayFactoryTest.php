<?php

namespace Mews\PosBundle\Tests\Unit\Gateway;

use Mews\Pos\PosInterface;
use Mews\PosBundle\Gateway\GatewayFactory;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Mews\PosBundle\Gateway\GatewayFactory
 */
class GatewayFactoryTest extends TestCase
{
    public function testThrowsForClassNotImplementingPosInterface(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('gateway_class must be implementation of ' . PosInterface::class);

        GatewayFactory::createPosGateway(
            'test',
            [
                'gateway_class'      => \stdClass::class,
                'credentials'        => [],
                'gateway_endpoints'  => [],
                'gateway_configs'    => [],
            ],
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(LoggerInterface::class),
            $this->createMock(ClientInterface::class),
        );
    }
}
