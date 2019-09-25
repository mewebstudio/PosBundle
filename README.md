# Türk bankaları için sanal pos paketi (Symfony 4)

## Temel Paket
[Pos](https://github.com/mewebstudio/pos)

### Minimum Gereksinimler
  - PHP >= 7.2
  - ext-dom
  - ext-json
  - ext-SimpleXML

###Kurulum
```bash
$ composer require mews/pos-bundle
```

`config/bundles.php` içerisine aşağıdaki satırı ekleyin
```php
<?php
return [
    // ...
    Mews\PosBundle\PosBundle::class => ['all' => true],
];
```

Aşağıdaki komutla ayarların bulunduğu yaml dosyasını proje ayar dizinine kopyalayın:
```bash
cp vendor/mews/pos-bundle/Resources/config/packages/pos.yaml config/packages/
```

###Kullanım
```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Mews\Pos\Exceptions\BankClassNullException;
use Mews\Pos\Exceptions\BankNotFoundException;
use Mews\PosBundle\DependencyInjection\PosService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    /**
     * @Route("/my-route", name="my-route")
     * @param Request $request
     * @param PosService $posService
     * @return Response
     * @throws BankClassNullException
     * @throws BankNotFoundException
     */
    public function index(Request $request, PosService $posService): Response
    {
        // Pos hesabını al
        $pos = $posService->getPos([
            'bank' => 'akbank',
            'model' => 'regular',
            'client_id' => 'XXXXXXXX',
            'username' => 'XXXXXXXX',
            'password' => 'XXXXXXXX',
            'env' => 'test', // test veya production. test ise; API Test Url, production ise; API Production URL kullanılır.
        ]);

        // Siparişi hazırla
        $pos->prepare([
            'id' => 'BENZERSIZ-SIPERIS-ID',
            'name' => 'John Doe', // zorunlu değil
            'email' => 'mail@customer.com', // zorunlu değil
            'user_id' => '12', // zorunlu değil
            'amount' => (double)20, // Sipariş tutarı
            'installment' => '0',
            'currency' => 'TRY',
            'ip' => $request->getClientIp(),
            'transaction' => 'pay', // pay => Auth, pre PreAuth (Direkt satış için pay, ön provizyon için pre)
        ]);

        // Ödemeyi yap
        $payment = $pos->payment([
            'number' => 'XXXXXXXXXXXXXXXX', // Kredi kartı numarası
            'month' => 'XX', // SKT ay
            'year' => 'XX', // SKT yıl, son iki hane
            'cvv' => 'XXX', // Güvenlik kodu, son üç hane
        ]);

        if ($payment->isSuccess()) {
            // ödeme başarılı
        } else {
            // Ödeme başarısız
        }

        // Sonuç
        dd($payment->response);
    }
}
```
