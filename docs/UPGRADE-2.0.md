# UPGRADE-2.0

Bu kılavuz, `mews/pos-bundle` v1.x'ten v2.0'a geçiş için Symfony uygulamanızda yapmanız gereken değişiklikleri açıklar.

`mews/pos` kütüphanesinin kendi API değişiklikleri (ödeme metodları, event'ler, vb.) için
[mews/pos UPGRADE-2.0](https://github.com/mewebstudio/pos) belgelerine bakın.

---

## 1. Symfony Sürümü

Symfony v4 desteği kaldırıldı. Minimum desteklenen Symfony sürümü **v5.4**'tür.

---

## 2. PHP Sürümü

Minimum PHP sürümü `>=7.4` → `>=8.0` olarak güncellendi.

---

## 3. `composer.json` güncellemesi

```bash
composer require mews/pos-bundle:^2.0
```

---

## 4. Konfigurasyon değişiklikleri

### 3.1 `enc_key` → `secret_key`

Tüm banka konfigurasyonlarında `enc_key` alanını `secret_key` olarak yeniden adlandırın:

```yaml
# Önce
credentials:
  enc_key: TRPXXXXX

# Sonra
credentials:
  secret_key: TRPXXXXX
```

### 3.2 `payment_model` kaldırıldı

`credentials` altındaki `payment_model` alanını kaldırın:

```yaml
# Önce
credentials:
  payment_model: !php/const Mews\Pos\PosInterface::MODEL_3D_SECURE
  merchant_id: ...

# Sonra
credentials:
  merchant_id: ...
```

### 3.3 `lang` → `gateway_configs.lang`

Banka düzeyindeki `lang` alanını `gateway_configs.lang` altına taşıyın:

```yaml
# Önce
estpos:
  gateway_class: ...
  lang: !php/const Mews\Pos\PosInterface::LANG_EN
  credentials: ...

# Sonra
estpos:
  gateway_class: ...
  credentials: ...
  gateway_configs:
    lang: !php/const Mews\Pos\PosInterface::LANG_EN
```

### 3.4 `test_mode` → `gateway_configs.test_mode`

Banka düzeyindeki `test_mode` v1.2.0'dan beri deprecated'dı, artık tamamen kaldırıldı.
`gateway_configs.test_mode` kullanın:

```yaml
# Önce
garanti:
  test_mode: true
  credentials: ...

# Sonra
garanti:
  credentials: ...
  gateway_configs:
    test_mode: true
```

### 3.5 `payment_api_2` kaldırıldı

ParamPos konfigurasyonundan `payment_api_2` ve `gateway_3d_host` endpoint'lerini kaldırın.
3D Host ödemeleri için ayrı bir `Param3DHostPos` banka girişi ekleyin:

```yaml
# Önce (tek girişle hem ParamPos hem 3D Host)
parampos:
  gateway_class: Mews\Pos\Gateways\ParamPos
  credentials:
    ...
  gateway_endpoints:
    payment_api: 'https://...service_turkpos_test.asmx'
    payment_api_2: 'https://...Service_Odeme.asmx'
    gateway_3d_host: 'https://...default.aspx'

# Sonra (iki ayrı giriş)
parampos:
  gateway_class: Mews\Pos\Gateway\ParamPos
  credentials:
    ...
  gateway_endpoints:
    payment_api: 'https://...service_turkpos_test.asmx'

param3dhostpos:
  gateway_class: Mews\Pos\Gateway\Param3DHostPos
  credentials:
    merchant_id: ...
    user_name: ...
    user_password: ...
    secret_key: ...  # GUID
  gateway_endpoints:
    payment_api: 'https://...Service_Odeme.asmx'
    gateway_3d_host: 'https://...default.aspx'
```

### 3.6 KuveytPos / VakifKatilimPos / PayFlexCPV4Pos — `gateway_3d` kaldırıldı

Bu üç gateway için `gateway_3d` endpoint'i artık gerekli değil; v2 kütüphanesi bu URL'yi `payment_api`'den türetiyor:

```yaml
# Önce
kuveytpos:
  gateway_endpoints:
    payment_api: 'https://boatest.../Home'
    gateway_3d: 'https://boatest.../Home/ThreeDModelPayGate'
    query_api: '...'

# Sonra
kuveytpos:
  gateway_endpoints:
    payment_api: 'https://boatest.../Home'
    query_api: '...'
```

---

## 5. Gateway sınıf adları

### 4.1 Namespace değişikliği

Tüm `gateway_class` değerlerinde `Mews\Pos\Gateways\` → `Mews\Pos\Gateway\` olarak güncelleyin:

```yaml
# Önce
gateway_class: Mews\Pos\Gateways\GarantiPos

# Sonra
gateway_class: Mews\Pos\Gateway\GarantiPos
```

### 4.2 Yeniden adlandırılan sınıflar

| v1 | v2                           |
|---|------------------------------|
| `Mews\Pos\Gateways\EstPos` | kaldırıldı                   |
| `Mews\Pos\Gateways\EstV3Pos` | `Mews\Pos\Gateway\AssecoPos` |
| `Mews\Pos\Gateways\PosNet` | `Mews\Pos\Gateway\PosNetPos` |

---

## 6. PHP sabit referansları

YAML konfigurasyonunda kullandığınız PHP sabit referanslarını güncelleyin:

```yaml
# Önce
mbr_id: !php/const Mews\Pos\Entity\Account\PayForAccount::MBR_ID_FINANSBANK

# Sonra
mbr_id: !php/const Mews\Pos\Model\Account\PayForPosAccount::MBR_ID_FINANSBANK
```

---

## 7. Yeni gateway'ler

v2.0 ile üç yeni gateway desteği eklendi:

### IyzicoPos
```yaml
iyzicopos:
  gateway_class: Mews\Pos\Gateway\IyzicoPos
  credentials:
    merchant_id: XXXX  # ApiKey
    secret_key: XXXX   # SecretKey
  gateway_endpoints:
    payment_api: 'https://sandbox-api.iyzipay.com'
    gateway_3d: 'https://sandbox-api.iyzipay.com'
```

### Param3DHostPos
```yaml
param3dhostpos:
  gateway_class: Mews\Pos\Gateway\Param3DHostPos
  credentials:
    merchant_id: XXXX    # CLIENT_CODE
    user_name: XXXX      # CLIENT_USERNAME
    user_password: XXXX  # CLIENT_PASSWORD
    secret_key: XXXX     # GUID
  gateway_endpoints:
    payment_api: 'https://...'
    gateway_3d_host: 'https://...'
```

### PayTrPos
```yaml
paytrpos:
  gateway_class: Mews\Pos\Gateway\PayTrPos
  credentials:
    merchant_id: XXXX    # MerchantId
    user_password: XXXX  # MerchantSalt
    secret_key: XXXX     # MerchantKey
  gateway_endpoints:
    payment_api: 'https://www.paytr.com/odeme/api'
    gateway_3d: 'https://www.paytr.com/odeme'
```

---

## 8. PHP kodu değişiklikleri

### `PosInterface` metodları

Uygulamanızda gateway nesnesinin `getAccount()` metodunu kullanıyorsanız aşağıdaki
isim değişikliklerine dikkat edin:

| v1 | v2 |
|---|---|
| `getAccount()->getBank()` | `getAccount()->getBankName()` |
| `getAccount()->getClientId()` | `getAccount()->getMerchantId()` |
| `getAccount()->getStoreKey()` | `getAccount()->getSecretKey()` |
| `getAccount()->getLang()` | Kaldırıldı (lang artık gateway_configs'te) |
| `$pos->getApiURL(...)` | Kaldırıldı |
| `$pos->getQueryAPIUrl()` | Kaldırıldı |

### EventListener gateway sınıf adları

EventListener'larınızda kullandığınız sınıf referanslarını güncelleyin:

```php
// Önce
if ($event->getGatewayClass() === \Mews\Pos\Gateways\EstV3Pos::class) { ... }

// Sonra
if ($event->getGatewayClass() === \Mews\Pos\Gateway\AssecoPos::class) { ... }
```

Diğer uygulama düzeyindeki değişiklikler için
[mews/pos UPGRADE-2.0](https://github.com/mewebstudio/pos) belgelerine bakın.
