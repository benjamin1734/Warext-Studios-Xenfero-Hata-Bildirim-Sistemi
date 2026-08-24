# Warext Studios | Xenfero Hata Bildirim Sistemi

XenForo 2.3 için kullanıcıların bulundukları sayfadan hata bildirimi gönderebildiği, teknik bilgileri otomatik toplayan ve raporları takip numarasıyla yöneten hata bildirim ve takip eklentisi.

## Hazır Kurulum ZIP

[XenForo'ya doğrudan yüklenebilir 1.0.1 ZIP paketini indir](Warext-Studios-Xenfero-Hata-Bildirim-Sistemi-1.0.1.zip)

Bu ZIP dosyasını açmayın. XenForo Admin CP içerisinde **Add-ons > Install/upgrade from archive** alanına ZIP dosyasını doğrudan yükleyin. **Code > Download ZIP** seçeneğiyle indirilen GitHub kaynak kod arşivini kullanmayın.

## Özellikler

- XenForo uyumlu **Hata Bildir** arayüzü
- Hata Bildir butonunu sağ altta sabit veya footer içinde gösterebilme
- Benzersiz `BUG-XXXXXXXX` takip numarası
- Kullanıcının kendi hata bildirimlerini ve yetkili cevaplarını takip edebilmesi
- URL, gerçek tarayıcı referrer bilgisi, tarayıcı, işletim sistemi, cihaz, ekran, viewport, tema ve dil bilgilerinin otomatik kaydı
- Güvenli ve sınırlı JavaScript hata kaydı
- Başarısız fetch/XHR isteklerinin güvenli teknik özeti
- XenForo sunucu hata günlüğü ile güven puanlı hata korelasyonu
- Thread, forum, kullanıcı, XFRM kaynak ve XFMG medya bağlamı algılama
- Ekran görüntüsü ve dosya ekleri için XenForo attachment sistemi
- ACP hata merkezi, filtreleme, atama, durum ve iç not yönetimi
- Kullanıcıya XenForo bildirimi ile yetkili cevapları ve durum değişiklikleri
- Yinelenen hata adayı tespiti ve yetkili onaylı birleştirme
- Yoğun hata sinyali tespiti
- Toplu durum, atama, çözüm ve arşiv işlemleri
- Çözülen sürüm ve çözüm notu takibi
- 7 / 30 / 90 günlük istatistik ekranı
- Kullanıcı bazlı 10 dakikalık flood koruması
- Ham IP saklamayan HMAC-SHA256 tabanlı ek IP flood koruması
- IP flood kotasının ACP üzerinden ayarlanabilmesi veya tamamen kapatılabilmesi
- IP flood sayaçlarının kısa süreli tutulup otomatik temizlenmesi
- XenForo izin sistemi
- Yeni kurulumda kayıtlı kullanıcılar için temel hata bildirim izinlerinin güvenli varsayılan uygulanması
- Mevcut özel Warext izinlerinin yükseltme sırasında korunması
- Hassas token/authorization verilerinin tanılama kayıtlarında maskelenmesi
- İsteğe bağlı yalnızca forum alan adı URL doğrulaması
- Eski teknik tanılama verileri için otomatik saklama süresi temizliği
- PHP 8.4 uyumlu XenForo 2.3 attachment/alert handler imzaları
- XFRM ve XFMG bağlamının yalnızca ilgili eklenti ve içerik gerçekten mevcutsa kabul edilmesi
- Yeniden açılan raporlarda eski çözüm bilgisinin otomatik temizlenmesi
- Kaldırma sırasında hata raporu attachment, alert ve IP flood sayaçlarının temizlenmesi
- Her push için PHP 8.4, XML, JSON ve JavaScript sözdizimi doğrulaması
- XenForo cron kimliği uzunluk sınırı dahil kurulum verisi kontrolleri

## Gereksinimler

- XenForo 2.3.0+
- PHP 8.0+

## Alternatif Manuel Kurulum

`upload` klasörünün içeriğini XenForo kurulum dizinine yükleyin ve Admin CP üzerinden `Warext/HataBildirimi` eklentisini kurun.

## Sürüm

`1.0.1`
