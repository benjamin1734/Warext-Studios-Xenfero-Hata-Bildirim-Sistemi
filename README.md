# Warext Studios | XenForo Bug Reporting System

## English

Warext Studios Bug Reporting System is a XenForo 2.3 add-on that lets users submit bug reports directly from the page they are viewing, automatically collects technical context, and manages reports with unique tracking numbers.

## Ready-to-install ZIP

[Download the latest XenForo-ready package](https://github.com/benjamin1734/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi/releases)

Do not extract the ZIP. Upload it directly from XenForo Admin CP through **Add-ons > Install/upgrade from archive**. Do not use GitHub's **Code > Download ZIP** source archive as the installation package.

## Usage

Registered users can report bugs, view their own reports, and attach files by default. A bug-icon **Report Bug** button is shown on forum pages, and its position can be configured from ACP > Bug Reporting System > Settings.

ACP contains a dedicated **Bug Reporting System** section with **Bug Reports**, **Prepared Replies**, **Statistics**, and **Settings** areas.

## Features

- XenForo-native **Report Bug** UI with unique `BUG-XXXXXXXX` tracking numbers
- Nine configurable Report Bug button positions
- Desktop table plus responsive mobile-card layout for ACP report lists
- Expanded issue-category selection and ACP category filtering
- Users can follow their own reports and staff replies
- Dedicated Bug Reporting System section in ACP
- XenForo 2.3 option-group-based settings page
- XenForo-standard option and option-explain phrase keys
- Clear administrator-facing setting titles and descriptions
- **New** label plus readable category/status labels in report lists
- Unlimited customizable, categorized, sortable prepared replies
- Theme-compatible prepared-reply cards with edit/delete controls
- Prepared-reply picker integrated into the reply form
- Prepared replies inserted into the current XenForo editor **without page reload**
- XenForo 2.3 `data-original-name` editor targeting
- Prepared replies editable in both WYSIWYG and BBCode modes
- Correct `XF:Editor` input handling to prevent empty-content errors
- BBCode rendering for user and staff replies
- Staff usernames displayed instead of raw user IDs in assignment/history views
- Optional automatic **In Progress** transition on first staff assignment
- XenForo alerts for staff replies, status changes, assignment changes, and resolution updates
- Internal notes excluded from user notifications
- Automatic capture of URL, referrer, browser, OS, device, screen, viewport, style/theme, and language information
- Safe JavaScript diagnostics and failed network-request logging
- Confidence-scored correlation with XenForo server error logs
- Context detection for threads, forums, users, XFRM, and XFMG content
- Screenshots and file attachments through XenForo's attachment system
- ACP filtering, assignment, status, internal notes, and bulk management
- Duplicate-bug candidate detection with staff-approved merging
- High-volume bug-signal detection with 7/30/90-day statistics
- Connection-based flood protection without storing users' raw IP addresses
- Automatic retention cleanup for diagnostic data
- PHP 8.4-compatible XenForo 2.3 handler signatures
- PHP, XML, JSON, JavaScript, and installation-ZIP validation on every push

## Requirements

- XenForo 2.3.0+
- PHP 8.0+

## Alternative manual installation

Upload the contents of the `upload` directory to the XenForo installation directory and install the `Warext/HataBildirimi` add-on from Admin CP.

## Support

For questions, bug reports, installation support, and help with Warext Studios XenForo add-ons, you can join our support Discord server:

**Discord:** https://discord.gg/tgsV5XMcFS

---

## Türkçe

XenForo 2.3 için kullanıcıların bulundukları sayfadan hata bildirimi gönderebildiği, teknik bilgileri otomatik toplayan ve raporları takip numarasıyla yöneten hata bildirim ve takip eklentisi.

## Hazır Kurulum ZIP

[En güncel XenForo kurulum paketini indir](https://github.com/benjamin1734/Warext-Studios-Xenfero-Hata-Bildirim-Sistemi/releases)

Bu ZIP dosyasını açmayın. XenForo Admin CP içerisinde **Add-ons > Install/upgrade from archive** alanına ZIP dosyasını doğrudan yükleyin. **Code > Download ZIP** seçeneğiyle indirilen GitHub kaynak kod arşivini kullanmayın.

## Kullanım

Kayıtlı kullanıcılar varsayılan olarak hata bildirebilir, kendi bildirimlerini görebilir ve dosya ekleyebilir. Forum sayfalarında böcek ikonlu **Hata Bildir** düğmesi görünür. Düğmenin konumu ACP > Hata Bildirim Sistemi > Ayarlar bölümünden değiştirilebilir.

ACP tarafında bağımsız **Hata Bildirim Sistemi** bölümü bulunur. Bu bölüm altında **Hata Bildirimleri**, **Hazır Cevaplar**, **İstatistikler** ve **Ayarlar** alanları yer alır.

## Özellikler

- XenForo uyumlu **Hata Bildir** arayüzü ve benzersiz `BUG-XXXXXXXX` takip numarası
- Hata Bildir düğmesi için dokuz farklı yerleşim seçeneği
- ACP hata raporu listesinde masaüstü tablo + mobil kart görünümü
- Genişletilmiş sorun türü seçimi ve ACP kategori filtresi
- Kullanıcının kendi hata bildirimlerini ve yetkili cevaplarını takip edebilmesi
- ACP'de bağımsız Hata Bildirim Sistemi yönetim bölümü
- XenForo 2.3 option group tabanlı Ayarlar sayfası
- XenForo standardına uygun option / option_explain phrase anahtarları
- Açık ve anlaşılır Türkçe ayar başlıkları ve açıklamaları
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

## Destek

Sorularınız, hata bildirimleriniz, kurulum desteği ve Warext Studios XenForo eklentileriyle ilgili yardım için destek Discord sunucumuza katılabilirsiniz:

**Discord:** https://discord.gg/tgsV5XMcFS

## Language support / Dil desteği

