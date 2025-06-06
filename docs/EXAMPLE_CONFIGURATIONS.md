## Konfigurasyon yapisi
Konfigurasyon için gereken parametreler gateway'den gateway'e değişir.
Gateway için zorunlu olan parametre sağlanmadığında hata alırsınız.

Olası konfigurasyonları görmek için:
```shell
php bin/console config:dump-reference mews_pos
```
örnek komut çıktısı
```yaml
mews_pos:
    banks:

        # Prototype
        name:
            gateway_class:        ~ # Required
            lang:                 tr
            credentials:          # Required
                payment_model:        ~ # Required

                # EstPos|ToslaPos: ClientId;
                # PosNet|GarantiPos|PayForPos|KuveytPos|VakifKatilimPos|PayFlexV4Pos|PayFlexCPV4Pos: MerchantId;
                # InterPos: ShopCode;
                # AkbankPos: MerchantSafeId;
                merchant_id:          ~
                sub_merchant_id:      ~

                # EstPos: KullanıcıAdı;
                # PosNet: PosNetId;
                # PayForPos|InterPos: UserCode;
                # GarantiPos: ProvUserID;
                # KuveytPos|VakifKatilimPos: UserName;
                # ToslaPos: ApiUser
                user_name:            ~

                # PosNet|GarantiPos|PayFlexV4Pos|PayFlexCPV4Pos: TerminalId
                # KuveytPos|VakifKatilimPos: CustomerId;
                # AkbankPos: TerminalSafeId
                terminal_id:          ~

                # PayFlexV4Pos|PayFlexCPV4Pos: Password;
                # EstPos: KullaniciSifresi;
                # PayForPos|InterPos: UserPass;
                # GarantiPos: ProvisionPassword;
                user_password:        ~

                # EstPos|GarantiPos: StoreKey;
                # PosNet: EncKey;
                # PayForPos|InterPos: MerchantPass;
                # KuveytPos: Password;
                enc_key:              ~

                # GarantiPos: ProvUserID;
                refund_user_name:     ~

                # GarantiPos: ProvisionPassword
                refund_user_password: ~
            gateway_endpoints:    # Required
                payment_api:          ~ # Required
                gateway_3d:           ~ # Required
                gateway_3d_host:      ~
                query_api:            ~
            test_mode:            false
```

Parametrelerin açıklamalarında hangi gateway'de neye karşılık geldiğini yazar.

Örneğin bu parametre açıklamasına göre
```yaml
                # EstPos|ToslaPos: ClientId;
                # PosNet|GarantiPos|PayForPos|KuveytPos|VakifKatilimPos|PayFlexV4Pos|PayFlexCPV4Pos: MerchantId;
                # InterPos: ShopCode;
                # AkbankPos: MerchantSafeId;
                merchant_id:          ~
```
`InterPos`'da **ShopCode** değeri için `merchant_id` alanı kullanmamız gerekiyor.


## Ornek Konfigurasyonlar:
```yaml
mews_pos:
  banks:
    estpos_payten:
      gateway_class: Mews\Pos\Gateways\EstV3Pos
      lang: !php/const Mews\Pos\PosInterface::LANG_TR #optional
      test_mode: true # optional
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 700XXXXXXX
        user_name: ISXXXXXXX #EstPos: kullanici adi
        user_password: ISXXXXXXX #EstPos: kullanici sifresi
        enc_key: TRPXXXXXXX
      gateway_endpoints:
        payment_api: 'https://entegrasyon.asseco-see.com.tr/fim/api'
        gateway_3d: 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate'
        gateway_3d_host: 'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate'
    yapikredi:
      gateway_class: Mews\Pos\Gateways\PosNet
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 670XXXXXXX # Üye İşyeri Numarası.
        terminal_id: 673XXXXXXX # Üye İşyeri Terminal Numaras
        user_name: 27XXXXXXX # Üye İşyeri POSNET Numarası
        enc_key: 10,43,43,45,65,56,76,08 # Şifreleme anahtar
      gateway_endpoints:
        payment_api: 'https://setmpos.ykb.com/PosnetWebService/XML'
        gateway_3d: 'https://setmpos.ykb.com/3DSWebService/YKBPaymentService'
    albaraka:
      gateway_class: Mews\Pos\Gateways\PosNetV1Pos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 670XXXXXXX # 10 haneli üye işyeri numarası
        terminal_id: XXXXXXXX # 8 haneli üye işyeri terminal numarası
        user_name: 10100628XXXXXXX # 16 haneli üye işyeri EPOS numarası.
        enc_key: 10,43,43,45,65,56,76,08 # Şifreleme anahtar
      gateway_endpoints:
        payment_api: 'https://epostest.albarakaturk.com.tr/ALBMerchantService/MerchantJSONAPI.svc'
        gateway_3d: 'https://epostest.albarakaturk.com.tr/ALBSecurePaymentUI/SecureProcess/SecureVerification.aspx'
    payfor_finansbank:
      gateway_class: Mews\Pos\Gateways\PayForPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 08530000XXXXXXXX # Üye İşyeri Numarası.
        user_name: QNB_API_XXXXXXXX # UserCode: Otorizasyon sistemi kullanıcı kodu.
        user_password: XXXXXXXX # Otorizasyon sistemi kullanıcı şifresi.
        enc_key: XXXXXXXX #  MerchantPass: 3D Secure şifresidir.
      gateway_endpoints:
        payment_api: 'https://vpostest.qnbfinansbank.com/Gateway/XMLGate.aspx'
        gateway_3d: 'https://vpostest.qnbfinansbank.com/Gateway/Default.aspx'
        gateway_3d_host: 'https://vpostest.qnbfinansbank.com/Gateway/3DHost.aspx'
    garanti:
      gateway_class: Mews\Pos\Gateways\GarantiPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 70XXXXXXXX # MerchantID
        user_name: XXXXXXXX # ProvUserID
        user_password: 123XXXXXXXX # ProvisionPassword
        terminal_id: 306XXXXXXXX
        enc_key: 123XXXXXXXX # StoreKey
        refund_user_name: PROXXXXXXXX # ProvUserID
        refund_user_password: 123qXXXXXXXX # ProvisionPassword
      gateway_endpoints:
        payment_api: 'https://sanalposprovtest.garantibbva.com.tr/VPServlet'
        gateway_3d: 'https://sanalposprovtest.garantibbva.com.tr/servlet/gt3dengine'
    interpos_denizbank:
      gateway_class: Mews\Pos\Gateways\InterPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: InterXXXXXXXX # ShopCode
        user_name: 31XXXXXXXX # UserCode
        user_password: 3XXXXXXXX  # UserPass
        enc_key: gXXXXXXXX # MerchantPass
      gateway_endpoints:
        payment_api: 'https://test.inter-vpos.com.tr/mpi/Default.aspx'
        gateway_3d: 'https://test.inter-vpos.com.tr/mpi/Default.aspx'
        gateway_3d_host: 'https://test.inter-vpos.com.tr/mpi/3DHost.aspx'
    kuveytpos:
      gateway_class: Mews\Pos\Gateways\KuveytPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 4XXXXXXXX # MerchantId: Üye işyerinin Kuveyt Türk SanalPos servisinde kayıtlı özel numarasıdır.
        terminal_id: 40XXXXXXXX # CustomerId: Üye işyerinin Kuveyt Türk'te yer SanalPos için kullanılabilecek hesaba ait müşteri numarasıdır.
        user_name: apiXXXXXXXX # UserName: https://kurumsal.kuveytturk.com.tr adresine login olarak kullanıcı işlemleri sayfasında APİ rolünde kullanıcı oluşturulmalıdır.
        enc_key: ApiXXXXXXXX # Password: Oluşturulan APİ kullanıcısının şifre bilgisidir.
      gateway_endpoints:
        payment_api: 'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home'
        gateway_3d: 'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home/ThreeDModelPayGate'
        query_api: 'https://boatest.kuveytturk.com.tr/BOA.Integration.WCFService/BOA.Integration.VirtualPos/VirtualPosService.svc?wsdl'
    vakifkatilim:
      gateway_class: Mews\Pos\Gateways\VakifKatilimPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 1XXXXXXXX # MerchantId: Üye işyerinin Kuveyt Türk SanalPos servisinde kayıtlı özel numarasıdır.
        terminal_id: 1XXXXXXXX # CustomerId: Üye işyerinin Kuveyt Türk'te yer SanalPos için kullanılabilecek hesaba ait müşteri numarasıdır.
        user_name: APIXXXXXXXX # UserName: https://kurumsal.kuveytturk.com.tr adresine login olarak kullanıcı işlemleri sayfasında APİ rolünde kullanıcı oluşturulmalıdır.
        enc_key: XXXXXXXX # Password: Oluşturulan APİ kullanıcısının şifre bilgisidir.
      gateway_endpoints:
        payment_api: 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home'
        gateway_3d: 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home/ThreeDModelPayGate'
        gateway_3d_host: 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/CommonPaymentPage/CommonPaymentPage'
    payflexv4_ziraat:
      gateway_class: Mews\Pos\Gateways\PayFlexV4Pos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 000000000XXXXXXXX # HostMerchantId: Üye işyeri numarası
        terminal_id: VPXXXXXXXX # HostTerminalNo: İşlemin hangi terminal üzerinden gönderileceği bilgisi
        user_password: 3XXXXXXXX # Password: Üye işyeri şifresi
      gateway_endpoints:
        payment_api: 'https://preprod.payflex.com.tr/Ziraatbank/VposWeb/v3/Vposreq.aspx'
        gateway_3d: 'https://preprod.payflex.com.tr/ZiraatBank/MpiWeb/MPI_Enrollment.aspx'
        query_api: 'https://sanalpos.ziraatbank.com.tr/v4/UIWebService/Search.aspx'
    payflexcpv4_vakifbank:
      gateway_class: Mews\Pos\Gateways\PayFlexCPV4Pos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_PAY
        merchant_id: 0001000XXXXXXXX # HostMerchantId: Üye işyeri numarası
        terminal_id: VPXXXXXXXX # HostTerminalNo: İşlemin hangi terminal üzerinden gönderileceği bilgisi
        user_password: XXXXXXXX # Password: Üye işyeri şifresi
      gateway_endpoints:
        payment_api: 'https://cptest.vakifbank.com.tr/CommonPayment/api/VposTransaction'
        gateway_3d: 'https://cptest.vakifbank.com.tr/CommonPayment/api/RegisterTransaction'
    akbankpos:
      gateway_class: Mews\Pos\Gateways\AkbankPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 20230904XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX # merchantSafeId: 32 karakter üye İş Yeri numarası
        terminal_id: 20230904XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX # terminalSafeId: 32 karakter
        # secretKey
        enc_key: 3230XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
      gateway_endpoints:
        payment_api: 'https://apipre.akbank.com/api/v1/payment/virtualpos'
        gateway_3d: 'https://virtualpospaymentgatewaypre.akbank.com/securepay'
        gateway_3d_host: 'https://virtualpospaymentgatewaypre.akbank.com/payhosting'
    toslapos:
      gateway_class: Mews\Pos\Gateways\ToslaPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_PAY
        merchant_id: 100XXXXXXXX # clientId
        user_name: POS_ENTXXXXXXXX # apiUser
        enc_key: POS_ENTXXXXXXXX # apiPass
      gateway_endpoints:
        payment_api: 'https://prepentegrasyon.tosla.com/api/Payment'
        gateway_3d: 'https://prepentegrasyon.tosla.com/api/Payment/ProcessCardForm'
        gateway_3d_host: 'https://prepentegrasyon.tosla.com/api/Payment/threeDSecure'
    parampos:
      gateway_class: Mews\Pos\Gateways\ParamPos
      credentials:
        payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
        merchant_id: 12345 #CLIENT_CODE Terminal ID
        user_name: TestUser #CLIENT_USERNAME Kullanıcı adı
        user_password: TestPassword #CLIENT_PASSWORD Şifre
        enc_key: kjsdfk-lkjdf-kjshdf-kjhfdsk-jfhshfsdfdsjf #GUID Üye İşyeri ait anahtarı
      gateway_endpoints:
        payment_api: 'https://test-dmz.param.com.tr/turkpos.ws/service_turkpos_test.asmx'
        #API URL for 3D host payment
        payment_api_2: 'https://test-pos.param.com.tr/to.ws/Service_Odeme.asmx'
        gateway_3d_host: 'https://test-pos.param.com.tr/default.aspx'
```
