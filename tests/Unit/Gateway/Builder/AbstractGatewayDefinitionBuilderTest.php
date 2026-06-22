<?php

namespace Mews\PosBundle\Tests\Unit\Gateway\Builder;

use Mews\Pos\Gateways\EstV3Pos;
use Mews\Pos\PosInterface;
use Mews\PosBundle\Exception\MissingExtensionException;
use Mews\PosBundle\Gateway\Builder\AbstractGatewayDefinitionBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mews\PosBundle\Gateway\Builder\AbstractGatewayDefinitionBuilder
 */
class AbstractGatewayDefinitionBuilderTest extends TestCase
{
    public function testThrowsMissingExtensionExceptionWhenExtensionNotLoaded(): void
    {
        $builder = new class extends AbstractGatewayDefinitionBuilder {
            public function supports(string $gatewayClass): bool { return true; }
            protected function getRequiredExtensions(): array
            {
                return ['this_extension_does_not_exist' => 'ext-nonexistent'];
            }
        };

        $this->expectException(MissingExtensionException::class);
        $this->expectExceptionMessage('ext-nonexistent');

        $builder->createDefinition('test', [
            'gateway_class'     => EstV3Pos::class,
            'credentials'       => [
                'payment_model' => PosInterface::MODEL_3D_SECURE,
                'merchant_id'   => '123',
                'user_name'     => 'user',
                'user_password' => 'pass',
                'enc_key'       => 'key',
            ],
            'gateway_endpoints' => [
                'payment_api' => 'https://api.example.com',
                'gateway_3d'  => 'https://3d.example.com',
            ],
        ]);
    }
}
