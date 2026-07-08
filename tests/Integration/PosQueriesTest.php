<?php

namespace Mews\PosBundle\Tests\Integration;

use Mews\Pos\PosQuery\PosQueryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversNothing
 */
class PosQueriesTest extends KernelTestCase
{
    private ContainerInterface $testContainer;

    protected function setUp(): void
    {
        parent::setUp();
        // getContainer() was added in Symfony 5.3; older versions require bootKernel() first
        if (method_exists(static::class, 'getContainer')) {
            $this->testContainer = static::getContainer();
        } else {
            // todo remove this when dropping support for Symfony 4
            static::bootKernel();
            $this->testContainer = static::$kernel->getContainer();
        }
    }

    public function testAssecoPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.asseco');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('asseco', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testPosNetQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.yapikredi');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('yapikredi', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testPosNetV1Query(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.albaraka');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('albaraka', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testPayForPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.payfor_finansbank');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('payfor_finansbank', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testPayForPosZiraatKatilimQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.payfor_ziraat_katilim');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('payfor_ziraat_katilim', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testGarantiPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.garanti');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('garanti', $query->getAccount()->getBankName());
        $this->assertSame(true, $query->isTestMode());
    }

    public function testInterPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.interpos_denizbank');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('interpos_denizbank', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testVakifKatilimPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.vakifkatilim');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('vakifkatilim', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testPayFlexV4PosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.payflexv4_ziraat');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('payflexv4_ziraat', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testPayFlexCPV4PosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.payflexcpv4_vakifbank');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('payflexcpv4_vakifbank', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testAkbankPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.akbankpos');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('akbankpos', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testToslaPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.toslapos');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('toslapos', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testParamPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.parampos');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('parampos', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testIyzicoPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.iyzicopos');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('iyzicopos', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }

    public function testPayTrPosQuery(): void
    {
        $query = $this->testContainer->get('test.mews_pos.query.paytrpos');
        $this->assertInstanceOf(PosQueryInterface::class, $query);
        $this->assertSame('paytrpos', $query->getAccount()->getBankName());
        $this->assertSame(false, $query->isTestMode());
    }
}
