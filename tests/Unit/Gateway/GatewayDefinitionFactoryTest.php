<?php

namespace Mews\PosBundle\Tests\Unit\Gateway;

use Mews\Pos\Gateway\AssecoPos;
use Mews\PosBundle\Gateway\GatewayDefinitionFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mews\PosBundle\Gateway\GatewayDefinitionFactory
 */
class GatewayDefinitionFactoryTest extends TestCase
{
    private GatewayDefinitionFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new GatewayDefinitionFactory();
    }

    public function testCreateDefinitionThrowsForUnknownGatewayClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No builder found for gateway class "App\NonExistentGateway" (bank: "mybank").');

        $this->factory->createDefinition('mybank', [
            'gateway_class' => 'App\NonExistentGateway',
        ]);
    }

    public function testCreateDefinitionReturnsDefinitionForKnownGatewayClass(): void
    {
        $definition = $this->factory->createDefinition('asseco', [
            'gateway_class'     => AssecoPos::class,
            'credentials'       => [
                'merchant_id'   => '700XXX',
                'user_name'     => 'user',
                'user_password' => 'pass',
                'secret_key'    => 'key',
            ],
            'gateway_endpoints' => [
                'payment_api' => 'https://example.com/api',
                'gateway_3d'  => 'https://example.com/3d',
            ],
            'gateway_configs'   => [],
        ]);

        $this->assertNotNull($definition);
    }
}
