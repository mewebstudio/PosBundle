# Türk bankaları için sanal pos paketi (Symfony 4|5|6|7)

## Temel Paket
[mews/pos](https://github.com/mewebstudio/pos)

## Ana başlıklar

- [Minimum Gereksinimler](#minimum-gereksinimler)
- [Kurulum](#kurulum)
- [Ornek 3D Secure Odeme](#farkli-banka-sanal-poslarini-eklemek)
- [Konfigurasyon Yapısı ve Örnekler](./docs/EXAMPLE_CONFIGURATIONS.md)
- [API ve 3D Form verisini degiştirme](./docs/EXAMPLE-API-ISTEK-VE-3D-FORM-VERSINI-DEGISTIRME.md)

## Minimum Gereksinimler
  - PHP >= 7.4
  - mews/pos ^1.6
  - Symfony 4|5|6|7

## Kurulum
1. 
    ```bash
    $ composer require mews/pos-bundle
    ```
2. `config/packages/mews_pos.yaml` dosyası oluşturun
3. ve içine bu alttaki config örneği ekleyin:
   ```yaml
   mews_pos:
     banks:
       estpos: # herhangi unique bir isim
         gateway_class: Mews\Pos\Gateways\EstV3Pos
         lang: !php/const Mews\Pos\PosInterface::LANG_TR # optional
         test_mode: false #optional, default: false;
         credentials:
           payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
           merchant_id: 700xxxxxx
           user_name: ISXXXXXX #EstPos: kullanici adi
           user_password: ISYYYYY #EstPos: kullanici sifresi
           enc_key: TRPXXXXX
         gateway_endpoints: # ilgili ortamin (test/prod) URL'leriyle degistiriniz:
           payment_api: 'https://entegrasyon.asseco-see.com.tr/fim/api' 
           gateway_3d: 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate'
           gateway_3d_host: 'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate' # optional, 3D Host ödemeler için zorunlu
       yapikredi:
         gateway_class: Mews\Pos\Gateways\PosNet
         credentials:
           payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
           merchant_id: 670XXXXXXX # Üye İşyeri Numarası.
           terminal_id: 67XXXXXX # Üye İşyeri Terminal Numaras
           user_name: 27XXX # Üye İşyeri POSNET Numarası
           enc_key: 10,92,92,02,02,02,02,02,01 # Şifreleme anahtar
         gateway_endpoints:
           payment_api: 'https://setmpos.ykb.com/PosnetWebService/XML'
           gateway_3d: 'https://setmpos.ykb.com/3DSWebService/YKBPaymentService'
   ```

### Ornek 3D Secure Odeme
```php
<?php

namespace App\Controller;

use Mews\Pos\Entity\Card\CreditCardInterface;
use Mews\Pos\Exceptions\CardTypeNotSupportedException;
use Mews\Pos\Exceptions\CardTypeRequiredException;
use Mews\Pos\Exceptions\HashMismatchException;
use Mews\Pos\Factory\CreditCardFactory;
use Mews\Pos\Gateways\PayFlexV4Pos;
use Mews\Pos\PosInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/payment/3d')]
class SingleBankThreeDSecurePaymentController extends AbstractController
{
    private string $paymentModel = PosInterface::MODEL_3D_HOST;

    public function __construct(
        /**
         * mews_pos.yaml'da birden fazla banka configurasyonu varsa, ilki default olarak inject edilecek
         */
        private PosInterface             $pos,
        /**
         * spesifik bir bankayi inject etmek istiyorsak:
         */
        private PosInterface             $yapikrediTest,
        private UrlGeneratorInterface    $urlGenerator,
        /**
         * birden fazla banka oldugunda bu sekilde hepsine erisebilirsiniz
         * @var PosInterface[]
         */
        #[TaggedIterator('mews_pos.gateway')]
        private iterable $banks,
    )
    {
//        foreach ($this->banks as $bank) {
//            if ('estpos' === $bank->getAccount()->getBank()) {
//               // todo
//            }
//        }
    }

    /**
     * Kullanicidan kredi kart bilgileri alip buraya POST ediyoruz
     */
    #[Route('/form', name: 'single_bank_payment_3d_redirect_form', methods: ['POST'])]
    public function form(Request $request)
    {
        $session = $request->getSession();

        $transaction = $request->get('tx', PosInterface::TX_TYPE_PAY_AUTH);

        $callbackUrl = $this->urlGenerator->generate('single_bank_payment_3d_response', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $order       = $this->createNewOrder(
            $this->paymentModel,
            $callbackUrl,
            $request->getClientIp(),
            $request->get('currency', PosInterface::CURRENCY_TRY),
            $request->get('installment'),
            $request->get('lang', PosInterface::LANG_TR)
        );
        $session->set('order', $order);

        $card = $this->createCard($this->pos, $request->request->all());

        /**
         * PayFlex'te provizyonu (odemeyi) tamamlamak icin tekrar kredi kart bilgileri isteniyor,
         * bu yuzden kart bilgileri kaydediyoruz
         */
        if ($this->pos::class === PayFlexV4Pos::class) {
            $session->set('card', $request->request->all());
        }
        $session->set('tx', $transaction);

        try {
            $formData = $this->pos->get3DFormData(
            $order,
            $this->paymentModel,
            $transaction,
            $card,
            /**
            * MODEL_3D_SECURE veya MODEL_3D_PAY ödemelerde kredi kart verileri olmadan
            * form verisini oluşturmak için true yapabilirsiniz.
            * Yine de bazı gatewaylerde kartsız form verisi oluşturulamıyor.
            */
            false
            );
        } catch (\Throwable $e) {
            dd($e);
        }

        return $this->render('redirect-form.html.twig', [
            'formData' => $formData,
        ]);
    }


    /**
     * kullanici bankadan geri buraya redirect edilir
     */
    #[Route('/response', name: 'single_bank_payment_3d_response')]
    public function response(Request $request)
    {
        $session = $request->getSession();

        $transaction = $session->get('tx', PosInterface::TX_TYPE_PAY_AUTH);

        // bankadan POST veya GET ile veri gelmesi gerekiyor
        if (($request->getMethod() !== 'POST')
            // PayFlex-CP GET request ile cevapliyor
            && ($request->getMethod() === 'GET' && ($this->pos::class !== \Mews\Pos\Gateways\PayFlexCPV4Pos::class || [] === $request->query->all()))
        ) {
            return new RedirectResponse($request->getBaseUrl());
        }

        $card = null;
        if ($this->pos::class === \Mews\Pos\Gateways\PayFlexV4Pos::class) {
            // bu gateway için ödemeyi tamamlarken tekrar kart bilgisi lazım.
            $savedCard = $session->get('card');
            $card      = $this->createCard($this->pos, $savedCard);
            $session->remove('card');
        }

        $order = $session->get('order');
        if (!$order) {
            throw new \Exception('Sipariş bulunamadı, session sıfırlanmış olabilir.');
        }

        try {
            $this->pos->payment($this->paymentModel, $order, $transaction, $card);
        } catch (HashMismatchException $e) {
            dd($e);
        } catch (\Exception|\Error $e) {
            dd($e);
        }

        if ($this->pos->isSuccess()) {
            echo 'success';
            dd($this->pos->getResponse());
        } else {
            dd($response);
        }
    }

    private function createNewOrder(
        string $paymentModel,
        string $callbackUrl,
        string $ip,
        string $currency,
        ?int   $installment = 0,
        string $lang = PosInterface::LANG_TR
    ): array
    {
        $orderId = date('Ymd').strtoupper(substr(uniqid(sha1(time())), 0, 4));

        $order = [
            'id'          => $orderId,
            'amount'      => 10.01,
            'currency'    => $currency,
            'installment' => $installment,
            'ip'          => \filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ? $ip : '127.0.0.1',
        ];

        if (\in_array($paymentModel, [
            PosInterface::MODEL_3D_SECURE,
            PosInterface::MODEL_3D_PAY,
            PosInterface::MODEL_3D_HOST,
            PosInterface::MODEL_3D_PAY_HOSTING,
        ], true)) {
            $order['success_url'] = $callbackUrl;
            $order['fail_url']    = $callbackUrl;
        }

        if ($lang) {
            //lang degeri verilmezse account (EstPosAccount) dili kullanilacak
            $order['lang'] = $lang;
        }

        return $order;
    }

    private function createCard(PosInterface $pos, array $card): CreditCardInterface
    {
        try {
            return CreditCardFactory::createForGateway(
                $pos,
                $card['number'],
                $card['year'],
                $card['month'],
                $card['cvv'],
                $card['name'],
                $card['type'] ?? null
            );
        } catch (CardTypeRequiredException|CardTypeNotSupportedException $e) {
            dd($e);
        } catch (\LogicException $e) {
            dd($e);
        }
    }
}
```

`redirect-form.html.twig`:
```html
{% if formData is iterable %}
   <form method="{{ formData.method }}" action="{{ formData.gateway }}" class="redirect-form" role="form">
      {% for key, value in formData.inputs %}
        <input type="hidden" name="{{ key }}" value="{{ value }}">
      {% endfor %}
      <div class="text-center">Redirecting...</div>
      <hr>
      <div class="form-group text-center">
         <button type="submit" class="btn btn-lg btn-block btn-success">Submit</button>
      </div>
   </form>
{% else %}
    {{ formData | raw }}
{% endif %}
```


PHP Sessioni kullanıyorsanız bu ayarları da yapmanız gerekiyor:
```yaml
# /config/packages/framework.yaml
framework:
   session:
        cookie_secure: true
        cookie_samesite: none
```

KuveytPos TDV2.0.0 için ekstra veri eklemek zorunludur.
Bunun EventListener ile yapabilirsiniz:
```php
<?php

namespace App\EventListener;

use Mews\Pos\Event\RequestDataPreparedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * KuveytPos TDV2.0.0 odemenin calismasi icin zorunlu eklenmesi gereken alan var.
 */
#[AsEventListener(event: RequestDataPreparedEvent::class)]
final class KuveytPosV2RequestDataPreparedEventListener
{
    public function __invoke(RequestDataPreparedEvent $event): void
    {
        if ($event->getGatewayClass() !== \Mews\Pos\Gateways\KuveytPos::class) {
            return;
        }

        /**
         * ekstra eklenmesi gereken verileri isteseniz $order icine ekleyip sonra o verilere
         * $event->getOrder() ile erisebilirsiniz.
         */
        $additionalRequestDataForKuveyt = [
            'DeviceData'     => [
                'DeviceChannel' => '02',
            ],
            'CardHolderData' => [
                'BillAddrCity'     => 'İstanbul',
                'BillAddrCountry'  => '792',
                'BillAddrLine1'    => 'XXX Mahallesi XXX Caddesi No 55 Daire 1',
                'BillAddrPostCode' => '34000',
                'BillAddrState'    => '40',
                'Email'            => 'xxxxx@gmail.com',
                'MobilePhone'      => [
                    'Cc'         => '90',
                    'Subscriber' => '1234567899',
                ],
            ],
        ];
        $requestData                    = $event->getRequestData();
        $requestData                    = \array_merge_recursive($requestData, $additionalRequestDataForKuveyt);
        $event->setRequestData($requestData);
    }
}
```


License
----

MIT
