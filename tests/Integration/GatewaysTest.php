<?php

namespace Mews\PosBundle\Tests\Integration;

use Mews\Pos\PosInterface;
use Mews\PosBundle\Tests\Kernel\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversNothing
 */
class GatewaysTest extends TestCase
{
    private ContainerInterface $container;
    private KernelInterface $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = new Kernel();
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
    }

    public function testEstPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.estpos');
        $this->assertInstanceOf(\Mews\Pos\Gateways\EstV3Pos::class, $pos);

        $this->assertSame('estpos', $pos->getAccount()->getBank());
        $this->assertSame('700XXXXXXXX', $pos->getAccount()->getClientId());
        $this->assertSame('ISBYYYY', $pos->getAccount()->getUsername());
        $this->assertSame('ISZZZZZ', $pos->getAccount()->getPassword());
        $this->assertSame('TRPWWWWW', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_EN, $pos->getAccount()->getLang());
        $this->assertSame('https://entegrasyon.asseco-see.com.tr/fim/api', $pos->getApiURL());
        $this->assertSame('https://entegrasyon.asseco-see.com.tr/fim/est3Dgate', $pos->get3DGatewayURL());
        $this->assertSame(
            'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPosNet(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.yapikredi');
        $this->assertInstanceOf(\Mews\Pos\Gateways\PosNet::class, $pos);

        $this->assertSame('yapikredi', $pos->getAccount()->getBank());
        $this->assertSame('670XXXXXX', $pos->getAccount()->getClientId());
        $this->assertSame('673YYYYYYY', $pos->getAccount()->getTerminalId());
        $this->assertSame('27212132321', $pos->getAccount()->getUsername());
        $this->assertSame('33,10,221,10,33,10,221,10', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame('https://setmpos.ykb.com/PosnetWebService/XML', $pos->getApiURL());
        $this->assertSame('https://setmpos.ykb.com/3DSWebService/YKBPaymentService', $pos->get3DGatewayURL());
        $this->assertSame(true, $pos->isTestMode());
    }

    public function testPosNetV1(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.albaraka');
        $this->assertInstanceOf(\Mews\Pos\Gateways\PosNetV1Pos::class, $pos);

        $this->assertSame('albaraka', $pos->getAccount()->getBank());
        $this->assertSame('670XXXXXX', $pos->getAccount()->getClientId());
        $this->assertSame('67YYYYY', $pos->getAccount()->getTerminalId());
        $this->assertSame('1010353453534', $pos->getAccount()->getUsername());
        $this->assertSame('33,10,221,10,33,10,221,10', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame(
            'https://epostest.albarakaturk.com.tr/ALBMerchantService/MerchantJSONAPI.svc/Sale',
            $pos->getApiURL(PosInterface::TX_TYPE_PAY_AUTH)
        );
        $this->assertSame('https://setmpos.ykb.com/3DSWebService/YKBPaymentService', $pos->get3DGatewayURL());
        $this->assertSame(true, $pos->isTestMode());
    }

    public function testPayForPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.payfor_finansbank');
        $this->assertInstanceOf(\Mews\Pos\Gateways\PayForPos::class, $pos);

        $this->assertSame('payfor_finansbank', $pos->getAccount()->getBank());
        $this->assertSame('08530313141242', $pos->getAccount()->getClientId());
        $this->assertSame('QNB_API_USERNAME', $pos->getAccount()->getUsername());
        $this->assertSame('UXXXXX', $pos->getAccount()->getPassword());
        $this->assertSame('12345678', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame('https://vpostest.qnbfinansbank.com/Gateway/XMLGate.aspx', $pos->getApiURL());
        $this->assertSame('https://vpostest.qnbfinansbank.com/Gateway/Default.aspx', $pos->get3DGatewayURL());
        $this->assertSame(
            'https://vpostest.qnbfinansbank.com/Gateway/3DHost.aspx',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(true, $pos->isTestMode());
    }

    public function testGarantiPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.garanti');
        $this->assertInstanceOf(\Mews\Pos\Gateways\GarantiPos::class, $pos);

        $this->assertSame('garanti', $pos->getAccount()->getBank());
        $this->assertSame('7001212', $pos->getAccount()->getClientId());
        $this->assertSame('PROVAUT', $pos->getAccount()->getUsername());
        $this->assertSame('123PASSWORD', $pos->getAccount()->getPassword());
        $this->assertSame('30691298', $pos->getAccount()->getTerminalId());
        $this->assertSame('12345678', $pos->getAccount()->getStoreKey());
        $this->assertSame('PROVRFN', $pos->getAccount()->getRefundUsername());
        $this->assertSame('R123PASSWORD', $pos->getAccount()->getRefundPassword());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame('https://sanalposprovtest.garantibbva.com.tr/VPServlet', $pos->getApiURL());
        $this->assertSame('https://sanalposprovtest.garantibbva.com.tr/servlet/gt3dengine', $pos->get3DGatewayURL());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testInterPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.interpos_denizbank');
        $this->assertInstanceOf(\Mews\Pos\Gateways\InterPos::class, $pos);

        $this->assertSame('interpos_denizbank', $pos->getAccount()->getBank());
        $this->assertSame('InterTestApi', $pos->getAccount()->getClientId());
        $this->assertSame('3123', $pos->getAccount()->getUsername());
        $this->assertSame('3', $pos->getAccount()->getPassword());
        $this->assertSame('gDg1N', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame('https://test.inter-vpos.com.tr/mpi/Default.aspx', $pos->getApiURL());
        $this->assertSame('https://test.inter-vpos.com.tr/mpi/Default.aspx', $pos->get3DGatewayURL());
        $this->assertSame(
            'https://test.inter-vpos.com.tr/mpi/3DHost.aspx',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testKuveytPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.kuveytpos');
        $this->assertInstanceOf(\Mews\Pos\Gateways\KuveytPos::class, $pos);

        $this->assertSame('kuveytpos', $pos->getAccount()->getBank());
        $this->assertSame('496', $pos->getAccount()->getClientId());
        $this->assertSame('400235', $pos->getAccount()->getCustomerId());
        $this->assertSame('apiuser1', $pos->getAccount()->getUsername());
        $this->assertSame('Api1232', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame(
            'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home/ThreeDModelProvisionGate',
            $pos->getApiURL(PosInterface::TX_TYPE_PAY_AUTH, PosInterface::MODEL_3D_SECURE)
        );
        $this->assertSame('https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home/ThreeDModelPayGate', $pos->get3DGatewayURL());
        $this->assertSame('https://boatest.kuveytturk.com.tr/BOA.Integration.WCFService/BOA.Integration.VirtualPos/VirtualPosService.svc?wsdl', $pos->getQueryAPIUrl());
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testVakifKatilimPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.vakifkatilim');
        $this->assertInstanceOf(\Mews\Pos\Gateways\VakifKatilimPos::class, $pos);

        $this->assertSame('vakifkatilim', $pos->getAccount()->getBank());
        $this->assertSame('1', $pos->getAccount()->getClientId());
        $this->assertSame('11111', $pos->getAccount()->getCustomerId());
        $this->assertSame('APIUSER', $pos->getAccount()->getUsername());
        $this->assertSame('kdsnsksl', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame(
            'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home/ThreeDModelProvisionGate',
            $pos->getApiURL(PosInterface::TX_TYPE_PAY_AUTH, PosInterface::MODEL_3D_SECURE)
        );
        $this->assertSame(
            'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home/ThreeDModelPayGate',
            $pos->get3DGatewayURL()
        );
        $this->assertSame(
            'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/CommonPaymentPage/CommonPaymentPage',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPayFlexV4Pos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.payflexv4_ziraat');
        $this->assertInstanceOf(\Mews\Pos\Gateways\PayFlexV4Pos::class, $pos);

        $this->assertSame('payflexv4_ziraat', $pos->getAccount()->getBank());
        $this->assertSame('000000000111111', $pos->getAccount()->getClientId());
        $this->assertSame('VP000095', $pos->getAccount()->getTerminalId());
        $this->assertSame('3XTgER89as', $pos->getAccount()->getPassword());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame('https://preprod.payflex.com.tr/Ziraatbank/VposWeb/v3/Vposreq.aspx', $pos->getApiURL());
        $this->assertSame(
            'https://preprod.payflex.com.tr/ZiraatBank/MpiWeb/MPI_Enrollment.aspx',
            $pos->get3DGatewayURL()
        );
        $this->assertSame(
            'https://sanalpos.ziraatbank.com.tr/v4/UIWebService/Search.aspx',
            $pos->getQueryAPIUrl()
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testPayFlexCPV4Pos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.payflexcpv4_vakifbank');
        $this->assertInstanceOf(\Mews\Pos\Gateways\PayFlexCPV4Pos::class, $pos);

        $this->assertSame('payflexcpv4_vakifbank', $pos->getAccount()->getBank());
        $this->assertSame('000100000013506', $pos->getAccount()->getClientId());
        $this->assertSame('VP000579', $pos->getAccount()->getTerminalId());
        $this->assertSame('123456', $pos->getAccount()->getPassword());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame(
            'https://cptest.vakifbank.com.tr/CommonPayment/api/VposTransaction',
            $pos->getApiURL()
        );
        $this->assertSame(
            'https://cptest.vakifbank.com.tr/CommonPayment/api/RegisterTransaction',
            $pos->get3DGatewayURL()
        );
        $this->assertSame(false, $pos->isTestMode());
    }

    public function testAkbankPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.akbankpos');
        $this->assertInstanceOf(\Mews\Pos\Gateways\AkbankPos::class, $pos);

        $this->assertSame('akbankpos', $pos->getAccount()->getBank());
        $this->assertSame('2023093534534543535353543543', $pos->getAccount()->getClientId());
        $this->assertSame('2023042423423424242324242123', $pos->getAccount()->getTerminalId());
        $this->assertSame(
            '323032333039303431373530303230948048503543392420320234394385349058904932',
            $pos->getAccount()->getStoreKey()
        );
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame(
            'https://apipre.akbank.com/api/v1/payment/virtualpos/transaction/process',
            $pos->getApiURL(PosInterface::TX_TYPE_PAY_AUTH)
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
        $pos = $this->container->get('test.mews_pos.gateway.toslapos');
        $this->assertInstanceOf(\Mews\Pos\Gateways\ToslaPos::class, $pos);

        $this->assertSame('toslapos', $pos->getAccount()->getBank());
        $this->assertSame('1000000494', $pos->getAccount()->getClientId());
        $this->assertSame('POS_ENT_Test_001', $pos->getAccount()->getUsername());
        $this->assertSame('POS_ENT_Test_001!*!*', $pos->getAccount()->getStoreKey());
        $this->assertSame(PosInterface::LANG_TR, $pos->getAccount()->getLang());
        $this->assertSame(
            'https://prepentegrasyon.tosla.com/api/Payment/threeDPayment',
            $pos->getApiURL(PosInterface::TX_TYPE_PAY_AUTH, PosInterface::MODEL_3D_PAY)
        );
        $this->assertSame(
            'https://prepentegrasyon.tosla.com/api/Payment/ProcessCardForm',
            $pos->get3DGatewayURL()
        );
        $this->assertSame(
            'https://prepentegrasyon.tosla.com/api/Payment/threeDSecure/',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
        $this->assertSame(false, $pos->isTestMode());
    }


    public function testParamPos(): void
    {
        $pos = $this->container->get('test.mews_pos.gateway.parampos');
        $this->assertInstanceOf(\Mews\Pos\Gateways\ParamPos::class, $pos);

        $this->assertSame('parampos', $pos->getAccount()->getBank());
        $this->assertSame('12345', $pos->getAccount()->getClientId());
        $this->assertSame('TestUser', $pos->getAccount()->getUsername());
        $this->assertSame('TestPassword', $pos->getAccount()->getPassword());
        $this->assertSame('kjsdfk-lkjdf-kjshdf-kjhfdsk-jfhshfsdfdsjf', $pos->getAccount()->getStoreKey());
        $this->assertSame(
            'https://test-dmz.param.com.tr/turkpos.ws/service_turkpos_test.asmx',
            $pos->getApiURL()
        );
        $this->assertSame(
            'https://test-pos.param.com.tr/default.aspx',
            $pos->get3DGatewayURL(PosInterface::MODEL_3D_HOST)
        );
    }
}
