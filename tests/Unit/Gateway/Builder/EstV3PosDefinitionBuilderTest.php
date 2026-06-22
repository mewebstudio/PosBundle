<?php

namespace Mews\PosBundle\Tests\Unit\Gateway\Builder;

use Mews\Pos\Gateways\EstV3Pos;
use Mews\Pos\PosInterface;
use Mews\PosBundle\Gateway\Builder\EstV3PosDefinitionBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * @covers \Mews\PosBundle\Gateway\Builder\EstV3PosDefinitionBuilder
 * @covers \Mews\PosBundle\Gateway\Builder\AbstractGatewayDefinitionBuilder
 */
class EstV3PosDefinitionBuilderTest extends TestCase
{
    private EstV3PosDefinitionBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new EstV3PosDefinitionBuilder();
    }

    private function validOptions(): array
    {
        return [
            'gateway_class'     => EstV3Pos::class,
            'credentials'       => [
                'payment_model' => PosInterface::MODEL_3D_SECURE,
                'merchant_id'   => 'merchant',
                'user_name'     => 'user',
                'user_password' => 'pass',
                'enc_key'       => 'key',
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

    public function testThrowsWhenPaymentModelIsInvalid(): void
    {
        $options = $this->validOptions();
        $options['credentials']['payment_model'] = 'invalid_model';

        $this->expectException(InvalidOptionsException::class);
        $this->builder->createDefinition('estpos', $options);
    }
}
