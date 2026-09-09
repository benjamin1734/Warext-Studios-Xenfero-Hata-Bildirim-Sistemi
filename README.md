# Warext Studios | Xenfero Hata Bildirim Sistemi

XenForo 2.3 için kullanıcıların bulundukları sayfadan hata bildirimi gönderebildiği, teknik bilgileri otomatik toplayan ve raporları takip numarasıyla yöneten hata bildirim ve takip eklentisi.

## Hazır Kurulum ZIP

[XenForo'ya doğrudan yüklenebilir 1.1.2 ZIP paketini indir](https://github.com/benjamin1734/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi/releases/download/v1.1.2/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi-1.1.2.zip)

Bu ZIP dosyasını açmayın. XenForo Admin CP içerisinde **Add-ons > Install/upgrade from archive** alanına ZIP dosyasını doğrudan yükleyin. **Code > Download ZIP** seçeneğiyle indirilen GitHub kaynak kod arşivini kullanmayın.

## Kullanım

Kayıtlı kullanıcılar varsayılan olarak hata bildirebilir, kendi bildirimlerini görebilir ve dosya ekleyebilir. Forum sayfalarının sağ alt köşesinde böcek ikonlu **Hata Bildir** düğmesi görünür. Düğme ACP ayarlarından footer içinde gösterilecek şekilde de değiştirilebilir.

ACP tarafında bağımsız **Hata Bildirim Sistemi** bölümü bulunur. Bu bölüm altında **Hata Bildirimleri**, **Hazır Cevaplar**, **İstatistikler** ve **Ayarlar** alanları yer alır.

**1.1.2** ile Ayarlar sayfasının bazı kurulumlarda 404 vermesine neden olan XenForo option group veri biçimi düzeltildi. `_data/option_groups.xml` artık XenForo 2.3'ün beklediği `<group>` yapısını kullanır; eklenti 1.1.2'ye yükseltildiğinde ayar grubu yeniden doğru biçimde içe aktarılır.

Hata raporları listesi daha hızlı taranabilir hale getirildi. `new` durumundaki raporların en solunda **Yeni** etiketi gösterilir; sorun türleri **Sayfa, Görsel / tasarım, Özellik, Mobil, Performans, Yetki, Diğer** şeklinde Türkçe tema etiketleriyle ayrıştırılır. Durumlar da ham veritabanı anahtarı yerine Türkçe XenForo etiketleriyle gösterilir.

Hazır cevaplar ACP üzerinden sınırsız şekilde oluşturulabilir, kategorilere ayrılabilir, sıralanabilir, düzenlenebilir ve aktif/pasif yapılabilir. Kategori silindiğinde içindeki cevaplar kaybolmaz, **Kategorisiz** bölümüne taşınır. Hazır Cevaplar yönetim ekranındaki Düzenle / Sil işlemleri XenForo tema uyumlu buton grubunu kullanır.

Rapor detayında ayrı bir **Hazır cevap kullan** kutusu bulunmaz. Hazır cevap seçicisi doğrudan **Kullanıcıya cevap yaz** formunda, gönderme alanının altında yer alır. Bir şablon seçildiğinde sayfa yenilenmeden XenForo editörünün mevcut içeriği değiştirilir; WYSIWYG ve BBCode görünümü desteklenir ve metin gönderilmeden önce serbestçe düzenlenebilir.

Hazır cevap içeriği XenForo'nun varsayılan WYSIWYG/BBCode editörüyle hazırlanır. Editör gönderileri XenForo'nun `XF:Editor` controller plugin'i üzerinden okunur; böylece içerik dolu olduğu halde “Hazır cevap içeriği boş bırakılamaz” hatası oluşmaz. Aynı doğru editör işleme yöntemi yetkili ve kullanıcı cevap alanlarında da kullanılır.

Bir hata bildirimi ilk kez yetkili personele atanırsa, ayar açık olduğu sürece `Yeni` durumundaki rapor otomatik olarak `İnceleniyor` durumuna alınır. Yetkili cevapları, durum değişiklikleri, atama değişiklikleri ve çözüm bilgisi güncellemeleri kullanıcıya XenForo bildirimi oluşturur; iç notlar kullanıcıya bildirilmez. Rapor sahibi ile işlemi yapan yetkili aynı hesap olsa bile bildirim üretimi engellenmez.

Yetkili atama seçimlerinde kayıt işlemi kullanıcı ID'si ile yapılmaya devam eder ancak ACP arayüzünde ID yerine kullanıcı adları gösterilir. İşlem geçmişindeki yetkili alanı da kullanıcı adıyla gösterilir.

## Özellikler

- XenForo uyumlu **Hata Bildir** arayüzü ve benzersiz `BUG-XXXXXXXX` takip numarası
- Kullanıcının kendi hata bildirimlerini ve yetkili cevaplarını takip edebilmesi
- ACP'de bağımsız Hata Bildirim Sistemi yönetim bölümü
- Çalışan XenForo 2.3 option group tabanlı Ayarlar sayfası
- Hata listesinde en solda **Yeni** etiketi ve Türkçe kategori/durum etiketleri
- Özelleştirilebilir, kategorili ve sıralanabilir hazır cevap sistemi
- Kategori silindiğinde cevapları Kategorisiz bölümüne güvenli taşıma
- Tema uyumlu hazır cevap kategori/cevap kartları ve Düzenle / Sil butonları
- Hazır cevap seçicisini cevap formunun altında gösterme
- Hazır cevabı **sayfa yenilemeden** mevcut XenForo editörüne aktarma
- WYSIWYG ve BBCode görünümünde hazır cevap değiştirme
- XenForo `XF:Editor` girdisini doğru okuyarak boş içerik hatasını önleme
- Kullanıcı ve yetkili cevaplarında BBCode render desteği
- Atama ve işlem geçmişinde yetkili ID yerine kullanıcı adı
- İlk personel atamasında isteğe bağlı otomatik **İnceleniyor** iş akışı
- Yetkili cevabı, durum, atama ve çözüm değişikliklerinde XenForo bildirimi
- İç notları kullanıcı bildirim akışının dışında tutma
- Bildirimden ilgili hata raporuna doğrudan erişim
- URL, referrer, tarayıcı, işletim sistemi, cihaz, ekran, viewport, tema ve dil bilgilerinin otomatik kaydı
- Güvenli JavaScript ve başarısız ağ isteği tanılama kayıtları
- XenForo sunucu hata günlüğü ile güven puanlı korelasyon
- Thread, forum, kullanıcı, XFRM ve XFMG içerik bağlamı algılama
- XenForo attachment sistemiyle ekran görüntüsü ve dosya ekleri
- ACP filtreleme, atama, durum, iç not ve toplu işlem yönetimi
- Yinelenen hata adayı tespiti ve yetkili onaylı birleştirme
- Yoğun hata sinyali tespiti ve 7 / 30 / 90 günlük istatistik ekranı
- Kullanıcı ve HMAC-SHA256 tabanlı IP flood koruması
- Tanılama verileri için otomatik saklama süresi temizliği
- PHP 8.4 uyumlu XenForo 2.3 handler imzaları
- Her push için PHP, XML, JSON, JavaScript ve kurulum ZIP doğrulaması

## Gereksinimler

- XenForo 2.3.0+
- PHP 8.0+

## Alternatif Manuel Kurulum

`upload` klasörünün içeriğini XenForo kurulum dizinine yükleyin ve Admin CP üzerinden `Warext/HataBildirimi` eklentisini kurun.

## Sürüm

`1.1.2`
