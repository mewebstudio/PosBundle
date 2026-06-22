# Değişiklik Geçmişi

## [1.3.0] - 2026-06-22

### Eklendi
- Symfony v8 desteği eklendi.

### Düzeltildi
- Gateway nesneleri oluşturan kodlar iyileştirildi.

### Yeniden Düzenlendi
- Bundle konfigurasyonunda bundle tarafından desteklenmeyen bir `gateway_class` (örneğin `mews/pos`'a eklenen yeni gateway sınıfı)
  tanımlandığında artık açıklayıcı bir `InvalidArgumentException` fırlatılıyor. Eskiden sessizce ignore ediliyordu.
- KuveytPos için PHP `ext-soap` eklendi zorunluluğu kaldırıldı.