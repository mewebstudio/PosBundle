# Değişiklik Geçmişi

## [2.0.0] - 2026-07-??

### Kırıcı Değişiklikler
- **PHP minimum sürümü** `>=7.4` → `>=8.0`
- **mews/pos** `^1.x` → `^2.0`
- **Konfigurasyon — `credentials`:** `enc_key` alanı `secret_key` olarak yeniden adlandırıldı
- **Konfigurasyon — `credentials`:** `payment_model` alanı kaldırıldı
- **Konfigurasyon — `lang`:** Banka düzeyindeki `lang` alanı kaldırıldı; `gateway_configs.lang` altına taşındı
- **Konfigurasyon — `test_mode`:** Banka düzeyindeki `test_mode` (v1.2.0'dan beri deprecated) tamamen kaldırıldı; `gateway_configs.test_mode` kullanın
- **Konfigurasyon — `gateway_endpoints`:** `payment_api_2` kaldırıldı (Param 3D Host ödemeleri için ayrı `Param3DHostPos` gateway'i kullanın)
- **Konfigurasyon — KuveytPos / VakifKatilimPos / PayFlexCPV4Pos:** `gateway_3d` endpoint'i artık zorunlu değil; v2 bunu `payment_api`'den türetiyor
- **Gateway sınıf adları:** `Mews\Pos\Gateways\*` → `Mews\Pos\Gateway\*`
- **Gateway yeniden adlandırmalar:** `EstPos` ve `EstV3Pos` → `AssecoPos`; `PosNet` → `PosNetPos`
- **PHP sabit referanslar:** `Mews\Pos\Entity\Account\PayForAccount` → `Mews\Pos\Model\Account\PayForPosAccount`
- **Bundle `AccountFactory` sınıfı** silindi;

### Eklendi
- `IyzicoPos` gateway desteği (`Mews\Pos\Gateway\IyzicoPos`)
- `Param3DHostPos` gateway desteği (`Mews\Pos\Gateway\Param3DHostPos`) — ParamPos 3D Host ödemeleri için ayrı gateway
- `PayTrPos` gateway desteği (`Mews\Pos\Gateway\PayTrPos`)
- `gateway_configs.lang` ayarı tüm gateway'ler için geçerli

### Yükseltme
Detaylı yükseltme kılavuzu için [UPGRADE-2.0.md](docs/UPGRADE-2.0.md) dosyasına bakın.

---

## [1.3.0] - 2026-06-22

### Eklendi
- Symfony v8 desteği eklendi.

### Düzeltildi
- Gateway nesneleri oluşturan kodlar iyileştirildi.

### Yeniden Düzenlendi
- Bundle konfigurasyonunda bundle tarafından desteklenmeyen bir `gateway_class` (örneğin `mews/pos`'a eklenen yeni gateway sınıfı)
  tanımlandığında artık açıklayıcı bir `InvalidArgumentException` fırlatılıyor. Eskiden sessizce ignore ediliyordu.
- KuveytPos için PHP `ext-soap` eklendi zorunluluğu kaldırıldı.