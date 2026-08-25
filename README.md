# Warext Studios | Xenfero Hata Bildirim Sistemi

XenForo 2.3 için kullanıcıların bulundukları sayfadan hata bildirimi gönderebildiği, teknik bilgileri otomatik toplayan ve raporları takip numarasıyla yöneten hata bildirim ve takip eklentisi.

## Hazır Kurulum ZIP

[XenForo'ya doğrudan yüklenebilir 1.0.8 ZIP paketini indir](Warext-Studios-Xenfero-Hata-Bildirim-Sistemi-1.0.8.zip)

Bu ZIP dosyasını açmayın. XenForo Admin CP içerisinde **Add-ons > Install/upgrade from archive** alanına ZIP dosyasını doğrudan yükleyin. **Code > Download ZIP** seçeneğiyle indirilen GitHub kaynak kod arşivini kullanmayın.

## Kullanım

Kayıtlı kullanıcılar varsayılan olarak hata bildirebilir, kendi bildirimlerini görebilir ve dosya ekleyebilir. Forum sayfalarının sağ alt köşesinde böcek ikonlu, belirgin **Hata Bildir** düğmesi görünür. Düğmeye basıldığında XenForo overlay formu açılır. Düğme ACP ayarlarından footer içinde gösterilecek şekilde de değiştirilebilir.

ACP tarafında **Content > Hata Bildirimleri** üzerinden rapor listesi, filtreler, atama, durum, teknik tanılama, kullanıcı cevapları, iç notlar ve istatistiklere erişilir.

## Özellikler

- XenForo uyumlu **Hata Bildir** arayüzü
- Gönderim sonrası rapor detayına yönlendirme ve takip numaralı başarı bildirimi
- Sağ altta sabit, ikonlu ve belirgin Hata Bildir düğmesi
- Hata Bildir düğmesini footer içinde gösterebilme
- Benzersiz `BUG-XXXXXXXX` takip numarası
- Kullanıcının kendi hata bildirimlerini ve yetkili cevaplarını takip edebilmesi
- URL, gerçek tarayıcı referrer bilgisi, tarayıcı, işletim sistemi, cihaz, ekran, viewport, tema ve dil bilgilerinin otomatik kaydı
- Güvenli ve sınırlı JavaScript hata kaydı
- Başarısız fetch/XHR isteklerinin güvenli teknik özeti
- XenForo sunucu hata günlüğü ile güven puanlı hata korelasyonu
- Thread, forum, kullanıcı, XFRM kaynak ve XFMG medya bağlamı algılama
- Ekran görüntüsü ve dosya ekleri için XenForo attachment sistemi
- ACP hata merkezi, filtreleme, atama, durum ve iç not yönetimi
- Responsive ACP teknik bilgi kartları, kontrollü URL/stack taşma yönetimi ve okunabilir işlem geçmişi
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
- Registered kullanıcı grubuna otomatik hata bildirme / kendi raporunu görme / dosya ekleme izni
- Administrative grubuna ve gerçek ACP admin hesaplarına otomatik yönetim izinleri
- Mevcut özel Warext izinlerinin yükseltme sırasında korunması
- Hassas token/authorization verilerinin tanılama kayıtlarında maskelenmesi
- İsteğe bağlı yalnızca forum alan adı URL doğrulaması
- Eski teknik tanılama verileri için otomatik saklama süresi temizliği
- PHP 8.4 uyumlu XenForo 2.3 attachment/alert handler imzaları
- XFRM ve XFMG bağlamının yalnızca ilgili eklenti ve içerik gerçekten mevcutsa kabul edilmesi
- Admin ve public template'lerin XenForo 2.3 veri formatında tek `_data/templates.xml` dosyasında tutulması
- TO_MANY entity relation'larının XenForo finder join mekanizmasına zorla verilmemesi
- Her push için PHP 8.4, XML, JSON ve JavaScript sözdizimi doğrulaması
- XenForo cron kimliği uzunluk sınırı ve kritik template veri yapısı kontrolleri

## Gereksinimler

- XenForo 2.3.0+
- PHP 8.0+

## Alternatif Manuel Kurulum

`upload` klasörünün içeriğini XenForo kurulum dizinine yükleyin ve Admin CP üzerinden `Warext/HataBildirimi` eklentisini kurun.

## Sürüm

`1.0.8`
