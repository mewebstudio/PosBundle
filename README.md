# Türk bankaları için sanal pos paketi (Symfony 5|6|7|8)

## Temel Paket
[mews/pos](https://github.com/mewebstudio/pos)

## Ana başlıklar

- [Minimum Gereksinimler](#minimum-gereksinimler)
- [Kurulum](#kurulum)
- [Servis Kullanımı](#servis-kullanımı)
- [Örnek 3D Secure Ödeme](./docs/EXAMPLE_3D_SECURE_ODEME.md)
- [Konfigurasyon Yapısı ve Örnekler](./docs/EXAMPLE_CONFIGURATIONS.md)
- [API ve 3D Form verisini degiştirme](./docs/EXAMPLE-API-ISTEK-VE-3D-FORM-VERSINI-DEGISTIRME.md)
- [PosQuery Servisleri (geçmiş, taksit, BIN sorguları)](./docs/POS-QUERY.md)

## Minimum Gereksinimler
  - PHP >= 8.0
  - mews/pos ^2.0
  - Symfony 5|6|7|8

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
       asseco: # herhangi unique bir isim
         gateway_class: Mews\Pos\Gateway\AssecoPos
         credentials:
           merchant_id: 700xxxxxx
           user_name: ISXXXXXX #AssecoPos: kullanici adi
           user_password: ISYYYYY #AssecoPos: kullanici sifresi
           secret_key: TRPXXXXX
         gateway_endpoints: # ilgili ortamin (test/prod) URL'leriyle degistiriniz:
           payment_api: 'https://entegrasyon.asseco-see.com.tr/fim/api'
           gateway_3d: 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate'
           gateway_3d_host: 'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate' # optional, 3D Host ödemeler için zorunlu
         gateway_configs:
           lang: !php/const Mews\Pos\PosInterface::LANG_TR # optional, default: LANG_TR
       yapikredi:
         gateway_class: Mews\Pos\Gateway\PosNetPos
         credentials:
           merchant_id: 670XXXXXXX # Üye İşyeri Numarası.
           terminal_id: 67XXXXXX # Üye İşyeri Terminal Numarası
           user_name: 27XXX # Üye İşyeri POSNET Numarası
           secret_key: 10,92,92,02,02,02,02,02,01 # Şifreleme anahtar
         gateway_endpoints:
           payment_api: 'https://setmpos.ykb.com/PosnetWebService/XML'
           gateway_3d: 'https://setmpos.ykb.com/3DSWebService/YKBPaymentService'
   ```

Diğer banka konfigurasyon örnekleri için bkz. [Konfigurasyon Yapısı ve Örnekler](./docs/EXAMPLE_CONFIGURATIONS.md).

## Servis Kullanımı

### POS Gateway inject etme

`mews_pos.yaml`'daki **ilk banka** `PosInterface` tipiyle doğrudan inject edilebilir:

```php
use Mews\Pos\PosInterface;

class MyService
{
    public function __construct(private PosInterface $pos) {}
}
```

**Belirli bir bankayı** inject etmek için argüman adını `mews_pos.yaml`'daki banka anahtarıyla eşleştirin:

```php
use Mews\Pos\PosInterface;

class MyService
{
    public function __construct(
        private PosInterface $asseco,       // mews_pos.yaml'daki "asseco" bankası
        private PosInterface $yapikredi,    // mews_pos.yaml'daki "yapikredi" bankası
    ) {}
}
```

**Tüm bankalara** erişmek için `TaggedIterator` kullanın:

```php
use Mews\Pos\PosInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class MyService
{
    public function __construct(
        #[TaggedIterator('mews_pos.gateway')]
        private iterable $banks,
    ) {}
}
```

Tam controller örneği için bkz. [Örnek 3D Secure Ödeme](./docs/EXAMPLE_3D_SECURE_ODEME.md).

---

### PosQuery inject etme

`PosQueryInterface`, ödeme işlemiyle ilişkili olmayan banka sorguları (işlem geçmişi, taksit oranları, BIN sorgusu vb.) için kullanılır. Her gateway PosQuery desteği sunmaz; bundle yalnızca `mews/pos` kütüphanesinin o gateway için bir PosQuery sınıfı tanımladığı durumlarda servisi oluşturur.

`mews_pos.yaml`'daki **ilk banka** PosQuery destekliyorsa doğrudan inject edilebilir:

```php
use Mews\Pos\PosQuery\PosQueryInterface;

class MyService
{
    public function __construct(private PosQueryInterface $posQuery) {}
}
```

**Belirli bir bankayı** inject etmek için argüman adını banka anahtarıyla eşleştirin:

```php
use Mews\Pos\PosQuery\PosQueryInterface;

class MyService
{
    public function __construct(
        private PosQueryInterface $asseco,
        private PosQueryInterface $yapikredi,
    ) {}
}
```

**Tüm PosQuery servislerine** erişmek için:

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

Tam örnek ve desteklenen sorgu tipleri için bkz. [PosQuery Servisleri](./docs/POS-QUERY.md).

---

License
----

MIT
