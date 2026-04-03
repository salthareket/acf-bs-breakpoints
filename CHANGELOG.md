# Changelog — ACF Bootstrap Breakpoints

## 2.0.0 — 2026-03-31

### Added
- Field type'a göre dinamik settings paneli (type değişince ilgili ayarlar göster/gizle)
- Breakpoint tablosunda type-specific default value hücreleri (text, number, units, true_false, select, color_picker, image)
- "Kullanılacak Birimler" select2 (multiple, sortable) — units plugin API'sinden birim listesi çekilir
- "Varsayılan Birim" select — seçili birimlere göre dinamik güncellenir
- "Tümünü Seç / Temizle" link'leri (Kullanılacak Birimler alanı için)
- "Default Color" — color_picker tipi için global renk seçici
- "Default State" — true_false tipi için global switch
- "Default Image" — image tipi için global görsel yükleme
- "Default Selection" — select tipi için global seçenek (Choices'a bağlı dinamik)
- Global vs Tablo default priority UI (biri doluyken diğeri passive görünür)
- Choices textarea değişince tablodaki select default'lar + global select dinamik güncellenir
- Kullanılacak Birimler değişince tablodaki units default select'leri dinamik güncellenir
- Yeni field eklendiğinde otomatik settings init (setInterval ile)
- Breakpoint visibility tablosu (aktif/pasif switch, özel başlık, per-BP default)

### Changed
- Settings paneli tamamen yeniden yazıldı
- Admin JS tamamen yeniden yazıldı (ACF Pro 6.x uyumlu event handling)
- CSS yeniden yapılandırıldı (priority UI, mini units, type-specific containers)
- `acf/include_field_types` hook'u içinde class tanımı (acf_field not found hatası düzeltildi)

### Fixed
- Row opacity: sadece ilk td'deki enabled switch kontrol eder, default value switch'leri row'a karışmaz
- Field type değiştiğinde tablo default hücreleri anında güncellenir (tüm tipler gizli render, CSS toggle)

## 1.2.2

- Önceki stabil versiyon
