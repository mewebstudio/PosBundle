### Örnek 3D Secure Ödeme
```php
<?php

namespace App\Controller;

use Mews\Pos\Entity\Card\CreditCardInterface;
use Mews\Pos\Exceptions\CardTypeNotSupportedException;
use Mews\Pos\Exceptions\CardTypeRequiredException;
use Mews\Pos\Exceptions\HashMismatchException;
use Mews\Pos\Factory\CreditCardFactory;
use Mews\Pos\Gateway\PayFlexV4Pos;
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
         * mews_pos.yaml'da birden fazla banka configurasyonu varsa,
         * ilki default olarak inject edilecek
         */
        private PosInterface             $pos,
        private UrlGeneratorInterface    $urlGenerator,
    )
    {
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

        /**
         * Bazı 3D Host gateway'leri (VakifKatilimPos, KuveytPos, vb.) doğrudan bir yönlendirme URL'i döner:
         * method=GET ve inputs=[]. Bu durumda HTML form render etmek yerine doğrudan yönlendiriyoruz.
         */
        if (!is_string($formData) && $formData['method'] === 'GET' && [] === $formData['inputs']) {
            return new RedirectResponse($formData['gateway']);
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
            && ($request->getMethod() === 'GET' && ($this->pos::class !== \Mews\Pos\Gateway\PayFlexCPV4Pos::class || [] === $request->query->all()))
        ) {
            return new RedirectResponse($request->getBaseUrl());
        }

        $card = null;
        if ($this->pos::class === \Mews\Pos\Gateway\PayFlexV4Pos::class) {
            // bu gateway için ödemeyi tamamlarken tekrar kart bilgisi lazım.
            $savedCard = $session->get('card');
            $card      = $this->createCard($this->pos, $savedCard);
            $session->remove('card');
        }

        $order = $session->get('order');
        if (!$order) {
            throw new \Exception('Sipariş bulunamadı, session sıfırlanmış olabilir.');
        }

        // PayFlexCPV4 GET ile cevap veriyor, diğerleri POST
        $gatewayResponseData = $this->pos::class === \Mews\Pos\Gateway\PayFlexCPV4Pos::class
            ? $request->query->all()
            : $request->request->all();

        try {
            $response = $this->pos->payment($this->paymentModel, $order, $transaction, $card, $gatewayResponseData);
        } catch (HashMismatchException $e) {
            /**
             * Bankadan gelen verilerin bankaya ait olmadığında bu exception oluşur.
             * Veya Banka API bilgileriniz hatalı ise de oluşur.
             * Eğer kütühaneden dolayı hash doğrulama hatası alıyorsanız, issue oluşturunuz.
             * Issue çözülene kadar geçici olarak disable_3d_hash_check: true ayarla hash doğrulamasını devre dışı bırakabilirsiniz.
             * Güvenlik açısından disable_3d_hash_check: false olarak kullanılması tavsiye edilmez.
             */
            dd($e);
        } catch (\Exception|\Error $e) {
            dd($e);
        }

        if ($this->pos->isSuccess()) {
            echo 'success';
            dd($response);
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
            'ip'          => \filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : '127.0.0.1',
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
<script>
   document.querySelector('form.redirect-form').submit();
</script>
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
