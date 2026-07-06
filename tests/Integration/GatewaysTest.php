<?php

namespace Mews\PosBundle\Tests\Integration;

use Mews\Pos\Model\Account\PayForPosAccount;
use Mews\Pos\PosInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversNothing
 */
class GatewaysTest extends KernelTestCase
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

    public function testEstPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.estpos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\AssecoPos::class, $pos);

        $this->assertSame('estpos', $pos->getAccount()->getBankName());
        $this->assertSame('700XXXXXXXX', $pos->getAccount()->getMerchantId());
        $this->assertSame('ISBYYYY', $pos->getAccount()->getUsername());
        $this->assertSame('ISZZZZZ', $pos->getAccount()->getPassword());
        $this->assertSame('TRPWWWWW', $pos->getAccount()->getSecretKey());
        $this->assertSame('https://entegrasyon.asseco-see.com.tr/fim/est3Dgate', $pos->get3DGatewayURL());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPosNet(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.yapikredi');
        $this->assertInstanceOf(\Mews\Pos\Gateway\PosNetPos::class, $pos);

        $this->assertSame('yapikredi', $pos->getAccount()->getBankName());
        $this->assertSame('670XXXXXX', $pos->getAccount()->getMerchantId());
        $this->assertSame('673YYYYYYY', $pos->getAccount()->getTerminalId());
        $this->assertSame('27212132321', $pos->getAccount()->getUsername());
        $this->assertSame('33,10,221,10,33,10,221,10', $pos->getAccount()->getSecretKey());
        $this->assertSame('https://setmpos.ykb.com/3DSWebService/YKBPaymentService', $pos->get3DGatewayURL());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPosNetV1(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.albaraka');
        $this->assertInstanceOf(\Mews\Pos\Gateway\PosNetV1Pos::class, $pos);

        $this->assertSame('albaraka', $pos->getAccount()->getBankName());
        $this->assertSame('670XXXXXX', $pos->getAccount()->getMerchantId());
        $this->assertSame('67YYYYY', $pos->getAccount()->getTerminalId());
        $this->assertSame('1010353453534', $pos->getAccount()->getUsername());
        $this->assertSame('33,10,221,10,33,10,221,10', $pos->getAccount()->getSecretKey());
        $this->assertSame('https://setmpos.ykb.com/3DSWebService/YKBPaymentService', $pos->get3DGatewayURL());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPayForPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.payfor_finansbank');
        $this->assertInstanceOf(\Mews\Pos\Gateway\PayForPos::class, $pos);

        $this->assertSame('payfor_finansbank', $pos->getAccount()->getBankName());
        $this->assertSame('08530313141242', $pos->getAccount()->getMerchantId());
        $this->assertSame('QNB_API_USERNAME', $pos->getAccount()->getUsername());
        $this->assertSame('UXXXXX', $pos->getAccount()->getPassword());
        $this->assertSame('12345678', $pos->getAccount()->getSecretKey());
        $this->assertSame(PayForPosAccount::MBR_ID_FINANSBANK, $pos->getAccount()->getMbrId());
        $this->assertSame('https://vpostest.qnb.com.tr/Gateway/Default.aspx', $pos->get3DGatewayURL());
        $this->assertSame(
            'https://vpostest.qnb.com.tr/Gateway/3DHost.aspx',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPayForPosZiraatKatilim(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.payfor_ziraat_katilim');
        $this->assertInstanceOf(\Mews\Pos\Gateway\PayForPos::class, $pos);

        $this->assertSame('payfor_ziraat_katilim', $pos->getAccount()->getBankName());
        $this->assertSame('08530313141242', $pos->getAccount()->getMerchantId());
        $this->assertSame('QNB_API_USERNAME', $pos->getAccount()->getUsername());
        $this->assertSame('UXXXXX', $pos->getAccount()->getPassword());
        $this->assertSame('12345678', $pos->getAccount()->getSecretKey());
        $this->assertSame(PayForPosAccount::MBR_ID_ZIRAAT_KATILIM, $pos->getAccount()->getMbrId());
        $this->assertSame('https://payfortestziraatkatilim.cordisnetwork.com/Mpi/Default.aspx', $pos->get3DGatewayURL());
        $this->assertSame(
            'https://payfortestziraatkatilim.cordisnetwork.com/Mpi/3DHost.aspx',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testGarantiPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.garanti');
        $this->assertInstanceOf(\Mews\Pos\Gateway\GarantiPos::class, $pos);

        $this->assertSame('garanti', $pos->getAccount()->getBankName());
        $this->assertSame('7001212', $pos->getAccount()->getMerchantId());
        $this->assertSame('PROVAUT', $pos->getAccount()->getUsername());
        $this->assertSame('123PASSWORD', $pos->getAccount()->getPassword());
        $this->assertSame('30691298', $pos->getAccount()->getTerminalId());
        $this->assertSame('12345678', $pos->getAccount()->getSecretKey());
        $this->assertSame('PROVRFN', $pos->getAccount()->getRefundUsername());
        $this->assertSame('R123PASSWORD', $pos->getAccount()->getRefundPassword());
        $this->assertSame('https://sanalposprovtest.garantibbva.com.tr/servlet/gt3dengine', $pos->get3DGatewayURL());
        $this->assertSame(true, $pos->isTestMode());
    }

    public function testInterPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.interpos_denizbank');
        $this->assertInstanceOf(\Mews\Pos\Gateway\InterPos::class, $pos);

        $this->assertSame('interpos_denizbank', $pos->getAccount()->getBankName());
        $this->assertSame('InterTestApi', $pos->getAccount()->getMerchantId());
        $this->assertSame('3123', $pos->getAccount()->getUsername());
        $this->assertSame('3', $pos->getAccount()->getPassword());
        $this->assertSame('gDg1N', $pos->getAccount()->getSecretKey());
        $this->assertSame('https://test.inter-vpos.com.tr/mpi/Default.aspx', $pos->get3DGatewayURL());
        $this->assertSame(
            'https://test.inter-vpos.com.tr/mpi/3DHost.aspx',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testKuveytPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.kuveytpos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\KuveytPos::class, $pos);

        $this->assertSame('kuveytpos', $pos->getAccount()->getBankName());
        $this->assertSame('496', $pos->getAccount()->getMerchantId());
        $this->assertSame('400235', $pos->getAccount()->getCustomerId());
        $this->assertSame('apiuser1', $pos->getAccount()->getUsername());
        $this->assertSame('Api1232', $pos->getAccount()->getSecretKey());
        $this->assertSame(true, $pos->isTestMode());
    }

    public function testVakifKatilimPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.vakifkatilim');
        $this->assertInstanceOf(\Mews\Pos\Gateway\VakifKatilimPos::class, $pos);

        $this->assertSame('vakifkatilim', $pos->getAccount()->getBankName());
        $this->assertSame('1', $pos->getAccount()->getMerchantId());
        $this->assertSame('11111', $pos->getAccount()->getCustomerId());
        $this->assertSame('APIUSER', $pos->getAccount()->getUsername());
        $this->assertSame('kdsnsksl', $pos->getAccount()->getSecretKey());
        $this->assertSame(
            'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/CommonPaymentPage/CommonPaymentPage',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPayFlexV4Pos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.payflexv4_ziraat');
        $this->assertInstanceOf(\Mews\Pos\Gateway\PayFlexV4Pos::class, $pos);

        $this->assertSame('payflexv4_ziraat', $pos->getAccount()->getBankName());
        $this->assertSame('000000000111111', $pos->getAccount()->getMerchantId());
        $this->assertSame('VP000095', $pos->getAccount()->getTerminalId());
        $this->assertSame('3XTgER89as', $pos->getAccount()->getPassword());
        $this->assertSame(
            'https://preprod.payflex.com.tr/ZiraatBank/MpiWeb/MPI_Enrollment.aspx',
            $pos->get3DGatewayURL()
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPayFlexCPV4Pos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.payflexcpv4_vakifbank');
        $this->assertInstanceOf(\Mews\Pos\Gateway\PayFlexCPV4Pos::class, $pos);

        $this->assertSame('payflexcpv4_vakifbank', $pos->getAccount()->getBankName());
        $this->assertSame('000100000013506', $pos->getAccount()->getMerchantId());
        $this->assertSame('VP000579', $pos->getAccount()->getTerminalId());
        $this->assertSame('123456', $pos->getAccount()->getPassword());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testAkbankPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.akbankpos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\AkbankPos::class, $pos);

        $this->assertSame('akbankpos', $pos->getAccount()->getBankName());
        $this->assertSame('2023093534534543535353543543', $pos->getAccount()->getMerchantId());
        $this->assertSame('2023042423423424242324242123', $pos->getAccount()->getTerminalId());
        $this->assertSame(
            '323032333039303431373530303230948048503543392420320234394385349058904932',
            $pos->getAccount()->getSecretKey()
        );
        $this->assertSame(
            'https://virtualpospaymentgatewaypre.akbank.com/securepay',
            $pos->get3DGatewayURL()
        );
        $this->assertSame(
            'https://virtualpospaymentgatewaypre.akbank.com/payhosting',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testToslaPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.toslapos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\ToslaPos::class, $pos);

        $this->assertSame('toslapos', $pos->getAccount()->getBankName());
        $this->assertSame('1000000494', $pos->getAccount()->getMerchantId());
        $this->assertSame('POS_ENT_Test_001', $pos->getAccount()->getUsername());
        $this->assertSame('POS_ENT_Test_001!*!*', $pos->getAccount()->getSecretKey());
        $this->assertSame(
            'https://prepentegrasyon.tosla.com/api/Payment/ProcessCardForm',
            $pos->get3DGatewayURL()
        );
        $this->assertSame(
            'https://prepentegrasyon.tosla.com/api/Payment/threeDSecure',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testParamPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.parampos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\ParamPos::class, $pos);

        $this->assertSame('parampos', $pos->getAccount()->getBankName());
        $this->assertSame('12345', $pos->getAccount()->getMerchantId());
        $this->assertSame('TestUser', $pos->getAccount()->getUsername());
        $this->assertSame('TestPassword', $pos->getAccount()->getPassword());
        $this->assertSame('kjsdfk-lkjdf-kjshdf-kjhfdsk-jfhshfsdfdsjf', $pos->getAccount()->getSecretKey());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testParam3DHostPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.param3dhostpos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\Param3DHostPos::class, $pos);

        $this->assertSame('param3dhostpos', $pos->getAccount()->getBankName());
        $this->assertSame('12345', $pos->getAccount()->getMerchantId());
        $this->assertSame('TestUser', $pos->getAccount()->getUsername());
        $this->assertSame('TestPassword', $pos->getAccount()->getPassword());
        $this->assertSame('kjsdfk-lkjdf-kjshdf-kjhfdsk-jfhshfsdfdsjf', $pos->getAccount()->getSecretKey());
        $this->assertSame(
            'https://test-pos.param.com.tr/default.aspx',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testIyzicoPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.iyzicopos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\IyzicoPos::class, $pos);

        $this->assertSame('iyzicopos', $pos->getAccount()->getBankName());
        $this->assertSame('sandbox-afdb81ff-f87a-4552-b320-6e7e9743a5ba', $pos->getAccount()->getMerchantId());
        $this->assertSame('sandbox-TIlBhYFGFMoxFZXwS1NfG2XyKCJlNvOl', $pos->getAccount()->getSecretKey());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPayTrPos(): void
    {
        $pos = $this->testContainer->get('test.mews_pos.gateway.paytrpos');
        $this->assertInstanceOf(\Mews\Pos\Gateway\PayTrPos::class, $pos);

        $this->assertSame('paytrpos', $pos->getAccount()->getBankName());
        $this->assertSame('123456', $pos->getAccount()->getMerchantId());
        $this->assertSame('https://www.paytr.com/odeme', $pos->get3DGatewayURL());
        $this->assertSame(false, $pos->isTestMode());
    }
}
