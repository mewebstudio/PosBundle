## Konfigurasyon yapisi
Konfigurasyon için gereken parametreler gateway'den gateway'e değişir.
Gateway için zorunlu olan parametre sağlanmadığında hata alırsınız.

Olası konfigurasyonları görmek için:
```shell
php bin/console config:dump-reference mews_pos
```

Parametrelerin açıklamalarında hangi gateway'de neye karşılık geldiğini yazar.

Örneğin bu parametre açıklamasına göre:
```yaml
    # InterPos: ShopCode;
    merchant_id: ~
```
`InterPos`'da **ShopCode** değeri için `merchant_id` alanı kullanmamız gerekiyor.

> **Not:** `secret_key` alanı gateway'e göre farklı anlamlar taşır:
> - `IyzicoPos`: SecretKey
> - `PayTrPos`: MerchantKey (`user_password` = MerchantSalt, `merchant_id` = MerchantId)
> - `ParamPos` / `Param3DHostPos`: GUID
> - Diğerleri: StoreKey / EncKey / Password / ApiPass (ilgili banka belgelerine bakın)


## Ornek Konfigurasyonlar:
```yaml
mews_pos:
  banks:
    estpos_payten:
      gateway_class: Mews\Pos\Gateway\AssecoPos
      credentials:
        merchant_id: 700XXXXXXX
        user_name: ISXXXXXXX #AssecoPos: kullanici adi
        user_password: ISXXXXXXX #AssecoPos: kullanici sifresi
        secret_key: TRPXXXXXXX # StoreKey
      gateway_endpoints:
        payment_api: 'https://entegrasyon.asseco-see.com.tr/fim/api'
        gateway_3d: 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate'
      gateway_configs:
        lang: !php/const Mews\Pos\PosInterface::LANG_TR # optional, default tr
    yapikredi:
      gateway_class: Mews\Pos\Gateway\PosNetPos
      credentials:
        merchant_id: 670XXXXXXX # Üye İşyeri Numarası.
        terminal_id: 673XXXXXXX # Üye İşyeri Terminal Numarası
        user_name: 27XXXXXXX # Üye İşyeri POSNET Numarası
        secret_key: 10,43,43,45,65,56,76,08 # Şifreleme anahtar (EncKey)
      gateway_endpoints:
        payment_api: 'https://setmpos.ykb.com/PosnetWebService/XML'
        gateway_3d: 'https://setmpos.ykb.com/3DSWebService/YKBPaymentService'
    albaraka:
      gateway_class: Mews\Pos\Gateway\PosNetV1Pos
      credentials:
        merchant_id: 670XXXXXXX # 10 haneli üye işyeri numarası
        terminal_id: XXXXXXXX # 8 haneli üye işyeri terminal numarası
        user_name: 10100628XXXXXXX # 16 haneli üye işyeri EPOS numarası.
        secret_key: 10,43,43,45,65,56,76,08 # Şifreleme anahtar (EncKey)
      gateway_endpoints:
        payment_api: 'https://epostest.albarakaturk.com.tr/ALBMerchantService/MerchantJSONAPI.svc'
        gateway_3d: 'https://epostest.albarakaturk.com.tr/ALBSecurePaymentUI/SecureProcess/SecureVerification.aspx'
    payfor_finansbank:
      gateway_class: Mews\Pos\Gateway\PayForPos
      credentials:
        merchant_id: 08530000XXXXXXXX # Üye İşyeri Numarası.
        user_name: QNB_API_XXXXXXXX # UserCode: Otorizasyon sistemi kullanıcı kodu.
        user_password: XXXXXXXX # Otorizasyon sistemi kullanıcı şifresi.
        secret_key: XXXXXXXX # MerchantPass: 3D Secure şifresidir.
        mbr_id: !php/const Mews\Pos\Model\Account\PayForPosAccount::MBR_ID_FINANSBANK
      gateway_endpoints:
        payment_api: 'https://vpostest.qnb.com.tr/Gateway/XMLGate.aspx'
        gateway_3d: 'https://vpostest.qnb.com.tr/Gateway/Default.aspx'
        gateway_3d_host: 'https://vpostest.qnb.com.tr/Gateway/3DHost.aspx'
    payfor_ziraat_katilim:
      gateway_class: Mews\Pos\Gateway\PayForPos
      credentials:
        merchant_id: 08530000XXXXXXXX # Üye İşyeri Numarası.
        user_name: QNB_API_XXXXXXXX # UserCode: Otorizasyon sistemi kullanıcı kodu.
        user_password: XXXXXXXX # Otorizasyon sistemi kullanıcı şifresi.
        secret_key: XXXXXXXX # MerchantPass: 3D Secure şifresidir.
        mbr_id: !php/const Mews\Pos\Model\Account\PayForPosAccount::MBR_ID_ZIRAAT_KATILIM
      gateway_configs:
        # Ziraat Katilim için hash kontrolü çalışmıyor. O yüzden devre dışı bırakıyoruz.
        disable_3d_hash_check: true
      gateway_endpoints:
        payment_api: 'https://payfortestziraatkatilim.cordisnetwork.com/Mpi/XMLGate.aspx'
        gateway_3d: 'https://payfortestziraatkatilim.cordisnetwork.com/Mpi/Default.aspx'
        gateway_3d_host: 'https://payfortestziraatkatilim.cordisnetwork.com/Mpi/3DHost.aspx'
    garanti:
      gateway_class: Mews\Pos\Gateway\GarantiPos
      credentials:
        merchant_id: 70XXXXXXXX # MerchantID
        user_name: XXXXXXXX # ProvUserID
        user_password: 123XXXXXXXX # ProvisionPassword
        terminal_id: 306XXXXXXXX
        secret_key: 123XXXXXXXX # StoreKey
        refund_user_name: PROXXXXXXXX # ProvUserID
        refund_user_password: 123qXXXXXXXX # ProvisionPassword
      gateway_endpoints:
        payment_api: 'https://sanalposprovtest.garantibbva.com.tr/VPServlet'
        gateway_3d: 'https://sanalposprovtest.garantibbva.com.tr/servlet/gt3dengine'
      gateway_configs:
        test_mode: false # Test ortamı için true yapılması gerekir.
    interpos_denizbank:
      gateway_class: Mews\Pos\Gateway\InterPos
      credentials:
        merchant_id: InterXXXXXXXX # ShopCode
        user_name: 31XXXXXXXX # UserCode
        user_password: 3XXXXXXXX  # UserPass
        secret_key: gXXXXXXXX # MerchantPass
      gateway_endpoints:
        payment_api: 'https://test.inter-vpos.com.tr/mpi/Default.aspx'
        gateway_3d: 'https://test.inter-vpos.com.tr/mpi/Default.aspx'
        gateway_3d_host: 'https://test.inter-vpos.com.tr/mpi/3DHost.aspx'
    kuveytpos:
      gateway_class: Mews\Pos\Gateway\KuveytPos
      credentials:
        merchant_id: 4XXXXXXXX # MerchantId
        terminal_id: 40XXXXXXXX # CustomerId / MüşteriNo
        user_name: apiXXXXXXXX # UserName (APİ kullanıcısı)
        secret_key: ApiXXXXXXXX # StoreKey (APİ kullanıcısının şifresi)
      gateway_endpoints:
        payment_api: 'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home'
        query_api: 'https://boatest.kuveytturk.com.tr/BOA.Integration.WCFService/BOA.Integration.VirtualPos/VirtualPosService.svc/Basic'
      gateway_configs:
        test_mode: false
    vakifkatilim:
      gateway_class: Mews\Pos\Gateway\VakifKatilimPos
      credentials:
        merchant_id: 1XXXXXXXX # MerchantId: Üye işyerinin Kuveyt Türk SanalPos servisinde kayıtlı özel numarasıdır.
        terminal_id: 1XXXXXXXX # CustomerId: Üye işyerinin Kuveyt Türk'te yer SanalPos için kullanılabilecek hesaba ait müşteri numarasıdır.
        user_name: APIXXXXXXXX # UserName: https://kurumsal.kuveytturk.com.tr adresine login olarak kullanıcı işlemleri sayfasında APİ rolünde kullanıcı oluşturulmalıdır.
        secret_key: XXXXXXXX #  Password: Oluşturulan APİ kullanıcısının şifre bilgisidir.
      gateway_endpoints:
        payment_api: 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home'
        gateway_3d_host: 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/CommonPaymentPage/CommonPaymentPage'
    payflexv4_ziraat:
      gateway_class: Mews\Pos\Gateway\PayFlexV4Pos
      credentials:
        merchant_id: 000000000XXXXXXXX # HostMerchantId: Üye işyeri numarası
        terminal_id: VPXXXXXXXX # HostTerminalNo: İşlemin hangi terminal üzerinden gönderileceği bilgisi
        user_password: 3XXXXXXXX # Password: Üye işyeri şifresi
      gateway_endpoints:
        payment_api: 'https://preprod.payflex.com.tr/Ziraatbank/VposWeb/v3/Vposreq.aspx'
        gateway_3d: 'https://preprod.payflex.com.tr/ZiraatBank/MpiWeb/MPI_Enrollment.aspx'
        query_api: 'https://sanalpos.ziraatbank.com.tr/v4/UIWebService/Search.aspx'
    payflexcpv4_vakifbank:
      gateway_class: Mews\Pos\Gateway\PayFlexCPV4Pos
      credentials:
        merchant_id: 0001000XXXXXXXX # HostMerchantId
        terminal_id: VPXXXXXXXX # HostTerminalNo
        user_password: XXXXXXXX # Password
      gateway_endpoints:
        payment_api: 'https://cptest.vakifbank.com.tr/CommonPayment/api'
    akbankpos:
      gateway_class: Mews\Pos\Gateway\AkbankPos
      credentials:
        merchant_id: 20230904XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX # merchantSafeId (32 karakter) üye İş Yeri numarası
        terminal_id: 20230904XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX # terminalSafeId (32 karakter)
        secret_key: 3230XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX # SecretKey
      gateway_endpoints:
        payment_api: 'https://apipre.akbank.com/api/v1/payment/virtualpos'
        gateway_3d: 'https://virtualpospaymentgatewaypre.akbank.com/securepay'
        gateway_3d_host: 'https://virtualpospaymentgatewaypre.akbank.com/payhosting'
    toslapos:
      gateway_class: Mews\Pos\Gateway\ToslaPos
      credentials:
        merchant_id: 100XXXXXXXX # clientId
        user_name: POS_ENTXXXXXXXX # apiUser
        secret_key: POS_ENTXXXXXXXX # apiPass
      gateway_endpoints:
        payment_api: 'https://prepentegrasyon.tosla.com/api/Payment'
        gateway_3d: 'https://prepentegrasyon.tosla.com/api/Payment/ProcessCardForm'
        gateway_3d_host: 'https://prepentegrasyon.tosla.com/api/Payment/threeDSecure'
    parampos:
      gateway_class: Mews\Pos\Gateway\ParamPos
      credentials:
        merchant_id: 12345 # CLIENT_CODE Terminal ID
        user_name: TestUser # CLIENT_USERNAME Kullanıcı adı
        user_password: TestPassword # CLIENT_PASSWORD Şifre
        secret_key: kjsdfk-lkjdf-kjshdf # GUID Üye İşyeri ait anahtarı
      gateway_endpoints:
        payment_api: 'https://test-dmz.param.com.tr/turkpos.ws/service_turkpos_test.asmx'
    # ParamPos 3D Host ödemeleri için ayrı gateway kullanılır:
    param3dhostpos:
      gateway_class: Mews\Pos\Gateway\Param3DHostPos
      credentials:
        merchant_id: 12345 # CLIENT_CODE Terminal ID
        user_name: TestUser # CLIENT_USERNAME Kullanıcı adı
        user_password: TestPassword # CLIENT_PASSWORD Şifre
        secret_key: kjsdfk-lkjdf-kjshdf # GUID Üye İşyeri ait anahtarı
      gateway_endpoints:
        payment_api: 'https://test-pos.param.com.tr/to.ws/Service_Odeme.asmx'
        gateway_3d_host: 'https://test-pos.param.com.tr/default.aspx'
    iyzicopos:
      gateway_class: Mews\Pos\Gateway\IyzicoPos
      credentials:
        merchant_id: sandbox-XXXXXXXX # ApiKey
        secret_key: sandbox-XXXXXXXX # SecretKey
      gateway_endpoints:
        payment_api: 'https://sandbox-api.iyzipay.com'
        query_api: 'https://sandbox-api.iyzipay.com/v2/reporting/payment'
    paytrpos:
      gateway_class: Mews\Pos\Gateway\PayTrPos
      credentials:
        merchant_id: XXXXXXXX # MerchantId
        user_password: XXXXXXXX # MerchantSalt
        secret_key: XXXXXXXX # MerchantKey
      gateway_endpoints:
        payment_api: 'https://www.paytr.com'
        gateway_3d: 'https://www.paytr.com/odeme'
        gateway_3d_host: 'https://www.paytr.com/odeme/guvenli'
```
