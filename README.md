# Warext Studios | Xenfero Hata Bildirim Sistemi

XenForo 2.3 için kullanıcıların bulundukları sayfadan hata bildirimi gönderebildiği, teknik bilgileri otomatik toplayan ve raporları takip numarasıyla yöneten hata bildirim ve takip eklentisi.

## Hazır Kurulum ZIP

[XenForo'ya doğrudan yüklenebilir 1.1.0 ZIP paketini indir](https://github.com/benjamin1734/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi/releases/download/v1.1.0/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi-1.1.0.zip)

Bu ZIP dosyasını açmayın. XenForo Admin CP içerisinde **Add-ons > Install/upgrade from archive** alanına ZIP dosyasını doğrudan yükleyin. **Code > Download ZIP** seçeneğiyle indirilen GitHub kaynak kod arşivini kullanmayın.

## Kullanım

Kayıtlı kullanıcılar varsayılan olarak hata bildirebilir, kendi bildirimlerini görebilir ve dosya ekleyebilir. Forum sayfalarının sağ alt köşesinde böcek ikonlu, belirgin **Hata Bildir** düğmesi görünür. Düğmeye basıldığında XenForo overlay formu açılır. Düğme ACP ayarlarından footer içinde gösterilecek şekilde de değiştirilebilir.

ACP tarafında başka bir yönetim kategorisinin altında değil, bağımsız **Hata Bildirim Sistemi** bölümü bulunur. Bu bölüm altında **Hata Bildirimleri**, **Hazır Cevaplar**, **İstatistikler** ve **Ayarlar** alanları yer alır.

Bir hata bildirimi ilk kez yetkili personele atanırsa, ayar açık olduğu sürece `Yeni` durumundaki rapor otomatik olarak `İnceleniyor` durumuna alınır. Durum bildirimleri açıksa raporu oluşturan kullanıcı XenForo bildirimi alır. Durum değişiklikleri ve yetkili cevap bildirimleri ACP üzerinden ayrı ayrı kapatılabilir; kullanıcının XenForo bildirim tercihi ayrıca korunur. Bildirimdeki hata raporu bağlantısı doğrudan kullanıcının ilgili hata raporu sayfasına gider.

Hazır cevaplar ACP üzerinden sınırsız şekilde oluşturulabilir, düzenlenebilir, aktif/pasif yapılabilir ve sıralanabilir. **1.1.0** ile hazır cevaplar kategori mantığına geçirilmiştir. Kategoriler ayrı oluşturulur, sıralanır, düzenlenir ve silinebilir; bir kategori silindiğinde içindeki cevaplar kaybolmaz, otomatik olarak **Kategorisiz** bölümüne taşınır. Hazır cevap eklerken kategori seçilebilir ve rapor ekranındaki hazır cevap seçim alanı kategori başlıkları altında gruplanır.

Hazır cevap içeriği XenForo'nun varsayılan WYSIWYG/BBCode editörüyle hazırlanır. 1.1.0 sürümünde editör gönderileri XenForo'nun `XF:Editor` controller plugin'i üzerinden okunur; böylece içerik dolu olduğu halde “Hazır cevap içeriği boş bırakılamaz” hatası oluşmaz. Aynı doğru editör işleme yöntemi yetkili ve kullanıcı cevap alanlarına da uygulanır.

Yetkili atama seçimlerinde kayıt işlemi kullanıcı ID'si ile güvenli biçimde yapılmaya devam eder; ancak ACP arayüzünde ID yerine yetkililerin kullanıcı adları gösterilir. İşlem geçmişindeki yetkili alanı da kullanıcı adıyla gösterilir.

## Özellikler

- XenForo uyumlu **Hata Bildir** arayüzü
- Gönderim sonrası rapor detayına yönlendirme ve takip numaralı başarı bildirimi
- Sağ altta sabit, ikonlu ve belirgin Hata Bildir düğmesi
- Hata Bildir düğmesini footer içinde gösterebilme
- Benzersiz `BUG-XXXXXXXX` takip numarası
- Kullanıcının kendi hata bildirimlerini ve yetkili cevaplarını takip edebilmesi
- ACP'de bağımsız **Hata Bildirim Sistemi** yönetim bölümü
- Hata Bildirimleri / Hazır Cevaplar / İstatistikler / Ayarlar alt yönetim alanları
- Özelleştirilebilir ve sıralanabilir hazır cevap sistemi
- Hazır cevap kategorisi oluşturma, düzenleme, sıralama ve silme
- Kategori silindiğinde hazır cevapları Kategorisiz bölümüne güvenli taşıma
- Hazır cevapların kategori başlıkları altında gruplanmış ACP görünümü
- Rapor ekranındaki hazır cevap seçiminde kategori grupları
- Hazır cevaplarda XenForo varsayılan WYSIWYG/BBCode editörü
- XenForo `XF:Editor` girdisini doğru okuyarak boş içerik hatasını önleme
- Hazır cevabı rapor cevap editörüne yükleyip göndermeden önce düzenleyebilme
- Kullanıcı ve yetkili cevaplarında BBCode render desteği
- Atama alanlarında yetkili ID yerine kullanıcı adı gösterimi
- İşlem geçmişinde yetkili ID yerine kullanıcı adı gösterimi
- İlk personel atamasında isteğe bağlı otomatik **İnceleniyor** iş akışı
- Durum değişikliği bildirimlerini ACP'den açma / kapatma
- Yetkili cevap bildirimlerini ACP'den açma / kapatma
- Kullanıcıya XenForo bildirimi ile yetkili cevapları ve durum değişiklikleri
- Bildirimden ilgili hata raporuna doğrudan erişim
- URL, gerçek tarayıcı referrer bilgisi, tarayıcı, işletim sistemi, cihaz, ekran, viewport, tema ve dil bilgilerinin otomatik kaydı
- Güvenli ve sınırlı JavaScript hata kaydı
- Başarısız fetch/XHR isteklerinin güvenli teknik özeti
- XenForo sunucu hata günlüğü ile güven puanlı hata korelasyonu
- Thread, forum, kullanıcı, XFRM kaynak ve XFMG medya bağlamı algılama
- Ekran görüntüsü ve dosya ekleri için XenForo attachment sistemi
- ACP hata merkezi, filtreleme, atama, durum ve iç not yönetimi
- Responsive ACP teknik bilgi kartları, kontrollü URL/stack taşma yönetimi ve okunabilir işlem geçmişi
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

`1.1.0`
