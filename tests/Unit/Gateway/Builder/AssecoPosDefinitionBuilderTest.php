<?php

namespace Mews\PosBundle\Tests\Unit\Gateway\Builder;

use Mews\Pos\Gateway\AssecoPos;
use Mews\PosBundle\Gateway\Builder\AssecoPosDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * @covers \Mews\PosBundle\Gateway\Builder\AssecoPosDefinitionBuilder
 * @covers \Mews\PosBundle\Gateway\Builder\AbstractGatewayDefinitionBuilder
 */
class AssecoPosDefinitionBuilderTest extends TestCase
{
    private AssecoPosDefinitionBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new AssecoPosDefinitionBuilder();
    }

    private function validOptions(): array
    {
        return [
            'gateway_class'     => AssecoPos::class,
            'credentials'       => [
                'merchant_id'   => 'merchant',
                'user_name'     => 'user',
                'user_password' => 'pass',
                'secret_key'    => 'key',
            ],
            'gateway_endpoints' => [
                'payment_api' => 'https://api.example.com',
                'gateway_3d'  => 'https://3d.example.com',
            ],
        ];
    }

    public function testCreatesDefinitionWithValidOptions(): void
    {
        $definition = $this->builder->createDefinition('estpos', $this->validOptions());
        $this->assertNotNull($definition);
    }

    public function testSupportsAssecoPos(): void
    {
        $this->assertTrue($this->builder->supports(AssecoPos::class));
    }

    public function testThrowsWhenPaymentApiMissing(): void
    {
        $options = $this->validOptions();
        unset($options['gateway_endpoints']['payment_api']);

        $this->expectException(MissingOptionsException::class);
        $this->builder->createDefinition('estpos', $options);
    }

    public function testThrowsWhenGateway3dMissing(): void
    {
        $options = $this->validOptions();
        unset($options['gateway_endpoints']['gateway_3d']);

        $this->expectException(MissingOptionsException::class);
        $this->builder->createDefinition('estpos', $options);
    }

    public function testThrowsWhenUserNameMissing(): void
    {
        $options = $this->validOptions();
        unset($options['credentials']['user_name']);

        $this->expectException(MissingOptionsException::class);
        $this->builder->createDefinition('estpos', $options);
    }

    public function testThrowsWhenUserPasswordMissing(): void
    {
        $options = $this->validOptions();
        unset($options['credentials']['user_password']);

        $this->expectException(MissingOptionsException::class);
        $this->builder->createDefinition('estpos', $options);
    }
}
