# Warext Studios | Xenfero Hata Bildirim Sistemi

XenForo 2.3 için kullanıcıların bulundukları sayfadan hata bildirimi gönderebildiği, teknik bilgileri otomatik toplayan ve raporları takip numarasıyla yöneten hata bildirim ve takip eklentisi.

## Hazır Kurulum ZIP

[XenForo'ya doğrudan yüklenebilir 1.1.3 ZIP paketini indir](https://github.com/benjamin1734/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi/releases/download/v1.1.3/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi-1.1.3.zip)

Bu ZIP dosyasını açmayın. XenForo Admin CP içerisinde **Add-ons > Install/upgrade from archive** alanına ZIP dosyasını doğrudan yükleyin. **Code > Download ZIP** seçeneğiyle indirilen GitHub kaynak kod arşivini kullanmayın.

## Kullanım

Kayıtlı kullanıcılar varsayılan olarak hata bildirebilir, kendi bildirimlerini görebilir ve dosya ekleyebilir. Forum sayfalarının sağ alt köşesinde böcek ikonlu **Hata Bildir** düğmesi görünür. Düğme ACP ayarlarından footer içinde gösterilecek şekilde de değiştirilebilir.

ACP tarafında bağımsız **Hata Bildirim Sistemi** bölümü bulunur. Bu bölüm altında **Hata Bildirimleri**, **Hazır Cevaplar**, **İstatistikler** ve **Ayarlar** alanları yer alır.

### 1.1.3 değişiklikleri

**Hazır cevap otomatik yükleme düzeltildi.** XenForo 2.3 WYSIWYG editörü açıkken gerçek textarea alanının `name` değeri değiştiği için önceki sürüm editörü her zaman bulamıyordu. 1.1.3, XenForo'nun `data-original-name="message"` yapısını kullanır; ayrıca editor handler, Froala instance ve BBCode alanı için yedek yollar içerir. Hazır cevap seçildiği anda sayfa yenilenmeden mevcut editöre aktarılır.

**Sorun türleri genişletildi.** Kullanıcı formunda ve ACP filtresinde şu kategoriler bulunur: Sayfa, Görsel / tasarım, Buton / özellik, Mobil, Performans, Yetki / erişim, Hesap / giriş / profil, Konu / mesaj / editör, Bildirim / e-posta, Dosya / görsel yükleme, Arama / filtreleme, Bağlantı / yönlendirme ve Diğer. Yeni kategoriler backend doğrulamasında da kabul edilir ve ACP listesindeki Türkçe etiketlerle eşleşir.

**Ayar açıklamaları yeniden yazıldı.** ACP Ayarlar sayfasındaki teknik ve gereksiz uzun açıklamalar yerine her seçeneğin neyi değiştirdiğini doğrudan anlatan daha kısa Türkçe açıklamalar kullanılır. Güvenlik ve tanılama davranışı değiştirilmemiştir.

### Önceki önemli değişiklikler

1.1.2 ile Ayarlar sayfasının bazı kurulumlarda 404 vermesine neden olan XenForo option group veri biçimi düzeltildi. `_data/option_groups.xml` XenForo 2.3'ün beklediği `<group>` yapısını kullanır.

Hata raporları listesinde `new` durumundaki raporların en solunda **Yeni** etiketi gösterilir. Durum ve sorun türleri ham veritabanı değerleri yerine tema uyumlu Türkçe etiketlerle görüntülenir.

Hazır cevaplar ACP üzerinden sınırsız şekilde oluşturulabilir, kategorilere ayrılabilir, sıralanabilir, düzenlenebilir ve aktif/pasif yapılabilir. Kategori silindiğinde içindeki cevaplar kaybolmaz, **Kategorisiz** bölümüne taşınır.

Rapor detayında hazır cevap seçicisi doğrudan **Kullanıcıya cevap yaz** formunda yer alır. Hazır cevap gönderilmeden önce XenForo WYSIWYG/BBCode editöründe serbestçe değiştirilebilir.

Bir hata bildirimi ilk kez yetkili personele atanırsa, ayar açık olduğu sürece `Yeni` durumundaki rapor otomatik olarak `İnceleniyor` durumuna alınır. Yetkili cevapları, durum değişiklikleri, atama değişiklikleri ve çözüm bilgisi güncellemeleri kullanıcıya XenForo bildirimi oluşturur; iç notlar kullanıcıya bildirilmez.

Yetkili atama seçimlerinde kayıt işlemi kullanıcı ID'si ile yapılmaya devam eder ancak ACP arayüzünde ID yerine kullanıcı adları gösterilir. İşlem geçmişindeki yetkili alanı da kullanıcı adıyla gösterilir.

## Özellikler

- XenForo uyumlu **Hata Bildir** arayüzü ve benzersiz `BUG-XXXXXXXX` takip numarası
- Genişletilmiş sorun türü seçimi ve ACP kategori filtresi
- Kullanıcının kendi hata bildirimlerini ve yetkili cevaplarını takip edebilmesi
- ACP'de bağımsız Hata Bildirim Sistemi yönetim bölümü
- XenForo 2.3 option group tabanlı Ayarlar sayfası
- Açık ve anlaşılır Türkçe ayar açıklamaları
- Hata listesinde en solda **Yeni** etiketi ve Türkçe kategori/durum etiketleri
- Özelleştirilebilir, kategorili ve sıralanabilir hazır cevap sistemi
- Tema uyumlu hazır cevap kategori/cevap kartları ve Düzenle / Sil butonları
- Hazır cevap seçicisini cevap formunun altında gösterme
- Hazır cevabı **sayfa yenilemeden** mevcut XenForo editörüne aktarma
- XenForo 2.3 `data-original-name` editör hedefleme desteği
- WYSIWYG ve BBCode görünümünde hazır cevap değiştirme
- XenForo `XF:Editor` girdisini doğru okuyarak boş içerik hatasını önleme
- Kullanıcı ve yetkili cevaplarında BBCode render desteği
- Atama ve işlem geçmişinde yetkili ID yerine kullanıcı adı
- İlk personel atamasında isteğe bağlı otomatik **İnceleniyor** iş akışı
- Yetkili cevabı, durum, atama ve çözüm değişikliklerinde XenForo bildirimi
- İç notları kullanıcı bildirim akışının dışında tutma
- URL, referrer, tarayıcı, işletim sistemi, cihaz, ekran, viewport, tema ve dil bilgilerinin otomatik kaydı
- Güvenli JavaScript ve başarısız ağ isteği tanılama kayıtları
- XenForo sunucu hata günlüğü ile güven puanlı korelasyon
- Thread, forum, kullanıcı, XFRM ve XFMG içerik bağlamı algılama
- XenForo attachment sistemiyle ekran görüntüsü ve dosya ekleri
- ACP filtreleme, atama, durum, iç not ve toplu işlem yönetimi
- Yinelenen hata adayı tespiti ve yetkili onaylı birleştirme
- Yoğun hata sinyali tespiti ve 7 / 30 / 90 günlük istatistik ekranı
- Kullanıcı ve ham IP saklamayan bağlantı bazlı flood koruması
- Tanılama verileri için otomatik saklama süresi temizliği
- PHP 8.4 uyumlu XenForo 2.3 handler imzaları
- Her push için PHP, XML, JSON, JavaScript ve kurulum ZIP doğrulaması

## Gereksinimler

- XenForo 2.3.0+
- PHP 8.0+

## Alternatif Manuel Kurulum

`upload` klasörünün içeriğini XenForo kurulum dizinine yükleyin ve Admin CP üzerinden `Warext/HataBildirimi` eklentisini kurun.

## Sürüm

`1.1.3`
