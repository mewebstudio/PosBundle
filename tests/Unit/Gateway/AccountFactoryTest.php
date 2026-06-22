<?php

namespace Mews\PosBundle\Tests\Unit\Gateway;

use Mews\Pos\PosInterface;
use Mews\PosBundle\Gateway\AccountFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mews\PosBundle\Gateway\AccountFactory
 */
class AccountFactoryTest extends TestCase
{
    public function testThrowsDomainExceptionForUnknownGatewayClass(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('App\UnknownGateway');

        AccountFactory::createAccount(
            'App\UnknownGateway',
            'mybank',
            [
                'payment_model' => PosInterface::MODEL_3D_SECURE,
                'merchant_id'   => '123',
                'enc_key'       => 'key',
            ],
        );
    }
}
