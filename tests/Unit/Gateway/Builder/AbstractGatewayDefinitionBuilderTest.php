<?php

namespace Mews\PosBundle\Tests\Unit\Gateway\Builder;

use Mews\Pos\Gateway\AssecoPos;
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
            'gateway_class'     => AssecoPos::class,
            'credentials'       => [
                'merchant_id'   => '123',
                'user_name'     => 'user',
                'user_password' => 'pass',
            ],
            'gateway_endpoints' => [
                'payment_api' => 'https://api.example.com',
                'gateway_3d'  => 'https://3d.example.com',
            ],
        ]);
    }
}
