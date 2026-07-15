# PosQuery Servisleri

`mews/pos` v2 ile birlikte gelen `PosQueryInterface`, bir ödeme siparişiyle ilişkili **olmayan** banka sorguları için
kullanılır: işlem geçmişi, taksit oranları, taksit tutarları, BIN sorguları ve ham API çağrıları.

Bu bundle, `mews/pos` kütüphanesinde karşılığı olan her gateway için otomatik olarak bir PosQuery servisi
kaydeder. Servis ID'si `mews_pos.query.<bank_name>` formatındadır (örn. `mews_pos.query.asseco`).

> **Not:** Tüm gateway'ler PosQuery'yi desteklemez. Bundle, yalnızca `mews/pos` kütüphanesinin o gateway için
> bir PosQuery sınıfı tanımladığı durumlarda servisi oluşturur (örn. `KuveytPos` için PosQuery yoktur).

---

## Enjeksiyon

### Varsayılan (ilk banka)

`mews_pos.yaml`'daki ilk banka konfigurasyonu PosQuery desteğine sahipse,
`PosQueryInterface` doğrudan tip ile inject edilebilir:

```php
use Mews\Pos\PosQuery\PosQueryInterface;

class MyService
{
    public function __construct(private PosQueryInterface $posQuery) {}
}
```

### Belirli bir banka

İsimlendirilmiş argüman ile belirli bir bankayı inject edin (argüman adı `mews_pos.yaml`'daki anahtar adına eşit):

```php
use Mews\Pos\PosQuery\PosQueryInterface;

class MyService
{
    public function __construct(
        // mews_pos.yaml'daki banka adı "asseco" ise:
        private PosQueryInterface $asseco,
        private PosQueryInterface $yapikredi,
    ) {}
}
```

### Tüm bankalar (TaggedIterator)

```php
use Mews\Pos\PosQuery\PosQueryInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class MyService
{
    public function __construct(
        #[TaggedIterator('mews_pos.query')]
        private iterable $posQueries,
    ) {}
}
```

---

## Örnek kullanım

```php
<?php

namespace App\Controller;

use Mews\Pos\PosQuery\PosQueryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/query')]
class PosQueryController extends AbstractController
{
    public function __construct(
        // mews_pos.yaml'daki ilk banka (PosQuery destekliyorsa)
        private PosQueryInterface $posQuery,
        // ya da belirli bir banka:
        private PosQueryInterface $asseco,
        // ya da tüm bankalar:
        #[TaggedIterator('mews_pos.query')]
        private iterable $allQueries,
    ) {}

    #[Route('/history', methods: ['GET'])]
    public function history(): JsonResponse
    {
        $result = $this->posQuery->history([
            'start_date' => new \DateTime('-7 days'),
            'end_date'   => new \DateTime(),
        ]);

        return $this->json($result);
    }

    #[Route('/installment-rates/{bin}', methods: ['GET'])]
    public function installmentRates(string $bin): JsonResponse
    {
        if (!$this->posQuery::isSupportedQuery(PosQueryInterface::QUERY_TYPE_INSTALLMENT_RATES)) {
            return $this->json(['error' => 'Bu gateway taksit oranı sorgusunu desteklemiyor.'], 400);
        }

        $result = $this->posQuery->getInstallmentRates(['bin' => $bin]);

        return $this->json($result);
    }

    #[Route('/installment-prices/{bin}/{amount}', methods: ['GET'])]
    public function installmentPrices(string $bin, float $amount): JsonResponse
    {
        $result = $this->posQuery->getInstallmentPrices([
            'bin'    => $bin,
            'amount' => $amount,
        ]);

        return $this->json($result);
    }

    #[Route('/bin/{bin}', methods: ['GET'])]
    public function binList(string $bin): JsonResponse
    {
        $result = $this->posQuery->getBinList(['bin' => $bin]);

        return $this->json($result);
    }

    /**
     * Ham API isteği — yanıt normalize edilmez, bankadan olduğu gibi döner.
     */
    #[Route('/custom', methods: ['POST'])]
    public function customQuery(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        $data   = $request->toArray();
        $apiUrl = $request->query->get('api_url');

        $result = $this->posQuery->customQuery($data, $apiUrl ?: null);

        return $this->json($result);
    }
}
```

---

> Her gateway'in tam olarak hangi sorguları desteklediğini çalışma zamanında
> `PosQueryInterface::isSupportedQuery()` ile kontrol edebilirsiniz.

`KuveytPos` ve `Param3DHostPos` gibi bu listede olmayan gateway'ler için bundle PosQuery servisi **oluşturmaz**.
