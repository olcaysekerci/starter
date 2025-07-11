# Laravel Starter - Modern Modular Application

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12.x-red.svg" alt="Laravel Version">
<img src="https://img.shields.io/badge/Vue.js-3.x-green.svg" alt="Vue Version">
<img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP Version">
<img src="https://img.shields.io/badge/Inertia.js-2.x-purple.svg" alt="Inertia Version">
<img src="https://img.shields.io/badge/TailwindCSS-3.x-teal.svg" alt="Tailwind Version">
</p>

## 📖 Proje Hakkında

Bu proje, modern web uygulamaları geliştirmek için tasarlanmış kapsamlı bir Laravel starter kit'idir. Modüler mimari, kullanıcı yönetimi, rol & yetki sistemi, aktivite logları, mail bildirimleri ve yönetici paneli gibi temel özellikleri içerir.

### 🎯 Proje Vizyonu

Modern, ölçeklenebilir ve sürdürülebilir web uygulamaları geliştirmek için gerekli tüm temel bileşenlerin hazır olduğu bir başlangıç noktası sunmak.

## ✨ Ana Özellikler

### 🏗️ Modern Teknoloji Stack'i
- **Laravel 12** - En son PHP framework
- **Vue 3 + Composition API** - Reaktif frontend framework
- **Inertia.js** - Modern monolith yaklaşımı
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Hızlı build tool
- **Laravel Jetstream** - Gelişmiş authentication

### 🔐 Güvenlik & Kimlik Doğrulama
- **Çok faktörlü kimlik doğrulama (2FA)**
- **Laravel Sanctum** API authentication
- **Spatie Laravel Permission** rol & yetki yönetimi
- **Güvenlik middleware**'leri
- **Şifre politikaları**

### 📊 Yönetim Paneli
- **Modern, responsive admin panel**
- **Kullanıcı yönetimi** (CRUD, rol atama)
- **Rol & yetki yönetimi**
- **Aktivite logları** ve audit trail
- **Mail bildirimleri** ve log takibi
- **Sistem ayarları** yönetimi
- **Dashboard** ve istatistikler

### 🏢 Modüler Mimari
- **Bağımsız modüller** (User, ActivityLog, MailNotification, Settings, Dashboard)
- **Service-Repository pattern**
- **DTO (Data Transfer Objects)** kullanımı
- **Action classes** ile business logic ayrımı
- **Consistent exception handling**

### 🎨 Kullanıcı Deneyimi
- **Dark/Light mode** desteği
- **Responsive design**
- **Vue 3 composables** ile reusable logic
- **Modern UI components**
- **Toast notifications**
- **Modal sistemleri**

## 🚀 Hızlı Başlangıç

### Gereksinimler
- PHP 8.2+
- Composer
- Node.js 18+
- SQLite/MySQL/PostgreSQL

### Kurulum

1. **Projeyi klonlayın**
```bash
git clone <repository-url>
cd starter
```

2. **Backend bağımlılıklarını yükleyin**
```bash
composer install
```

3. **Frontend bağımlılıklarını yükleyin**
```bash
npm install
```

4. **Çevre dosyasını hazırlayın**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Veritabanını hazırlayın**
```bash
# SQLite için (varsayılan)
touch database/database.sqlite

# MySQL için .env dosyasını güncelleyin
```

6. **Veritabanını migrate edin**
```bash
php artisan migrate --seed
```

7. **Geliştirme sunucusunu başlatın**
```bash
# Tam geliştirme ortamı (önerilen)
composer dev

# Veya ayrı ayrı
php artisan serve
npm run dev
```

8. **Super admin kullanıcısı oluşturun**
```bash
php artisan create:super-admin
```

## 🏗️ Proje Mimarisi

### Modüler Yapı
```
app/Modules/
├── User/              # Kullanıcı yönetimi
├── Dashboard/         # Dashboard ve istatistikler
├── ActivityLog/       # Aktivite takibi
├── MailNotification/  # Mail yönetimi
└── Settings/         # Sistem ayarları
```

### Her Modül İçeriği
```
ModuleName/
├── Controllers/       # HTTP controllers
├── Services/         # Business logic
├── Repositories/     # Data access layer
├── Models/          # Eloquent models
├── DTOs/           # Data transfer objects
├── Actions/        # Specific operations
├── Requests/       # Form validation
├── Exceptions/     # Custom exceptions
├── Panel/routes.php    # Admin routes
├── Web/routes.php      # Frontend routes
└── ModuleNameServiceProvider.php
```

### Frontend Yapısı
```
resources/js/
├── Components/
│   ├── Panel/      # Admin panel components
│   ├── Shared/     # Jetstream shared components
│   └── Web/        # Frontend components
├── Layouts/        # Layout components
├── Modules/        # Module-specific components
├── Composables/    # Vue 3 composables
├── Pages/          # Page components
└── Utils/          # Utility functions
```

## 📋 Temel Kullanım

### Kullanıcı Yönetimi

**Yeni kullanıcı oluşturma:**
```php
$userService = new UserService();
$userData = new UserDTO([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'password' => 'password123'
]);
$user = $userService->create($userData);
```

**Rol atama:**
```php
$user->assignRole('admin');
$user->givePermissionTo('user.create');
```

### Aktivite Logları

Aktivite logları otomatik olarak kaydedilir:
```php
// Otomatik log kaydı
activity('user')
    ->performedOn($user)
    ->log('Kullanıcı oluşturuldu');
```

### Mail Bildirimleri

Mail gönderimi ve loglanması:
```php
$mailService = new MailNotificationService();
$mailService->sendMail($to, $subject, $content);
```

## 🔧 Geliştirme Komutları

### Backend Komutları
```bash
# Kod formatlama
vendor/bin/pint

# Test çalıştırma
composer test
php artisan test

# Cache temizleme
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Queue işleme
php artisan queue:listen --tries=1

# Log izleme
php artisan pail --timeout=0
```

### Frontend Komutları
```bash
# Geliştirme sunucusu
npm run dev

# Production build
npm run build
```

### Tam Geliştirme Ortamı
```bash
# Laravel server + Queue + Logs + Vite'ı aynı anda başlatır
composer dev
```

## 🔐 Güvenlik Özellikleri

### Kimlik Doğrulama
- **Laravel Jetstream** ile gelişmiş authentication
- **İki faktörlü kimlik doğrulama (2FA)**
- **Güvenli şifre sıfırlama**
- **Email doğrulama**

### Yetkilendirme
- **Spatie Laravel Permission** ile rol & yetki sistemi
- **Middleware tabanlı koruma**
- **API token yönetimi**
- **Resource-based permissions**

### Güvenlik Middleware'leri
- **UserSecurityMiddleware** - Kullanıcı güvenlik kontrolü
- **Rate limiting** - API call sınırlaması
- **CSRF protection** - Cross-site request forgery koruması

## 📊 Monitoring ve Loglar

### Aktivite Logları
- **Kullanıcı işlemleri** takibi
- **Sistem değişiklikleri** kaydı
- **Audit trail** oluşturma
- **Filtreleme** ve raporlama

### Mail Logları
- **Gönderilen mailler** takibi
- **Delivery status** kontrolü
- **Hata logları** ve retry mekanizması
- **Test mail** gönderimi

### Sistem Logları
- **Laravel log channels**
- **Real-time log monitoring** (Pail)
- **Error tracking**
- **Performance monitoring**

## 🎨 UI/UX Özellikleri

### Tasarım Sistemi
- **Consistent component library**
- **Dark/Light mode** geçişi
- **Responsive design** (mobile-first)
- **Accessibility** standartları

### Vue 3 Components
- **Composition API** kullanımı
- **Reusable composables**
- **Type-safe props**
- **Reactive state management**

### Kullanıcı Etkileşimi
- **Toast notifications**
- **Modal dialogs**
- **Loading states**
- **Form validations**

## 🧪 Test Stratejisi

### Test Türleri
- **Feature tests** - End-to-end functionality
- **Unit tests** - Individual components
- **Browser tests** - UI interactions
- **API tests** - Endpoint validations

### Test Komutları
```bash
# Tüm testleri çalıştır
composer test

# Specific test dosyası
php artisan test --filter UserTest

# Coverage raporu
php artisan test --coverage
```

## 📦 Deployment

### Production Hazırlığı
```bash
# Dependencies yükle
composer install --no-dev --optimize-autoloader

# Frontend build
npm run build

# Configuration cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migration
php artisan migrate --force

# Storage link
php artisan storage:link
```

### Environment Ayarları
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

## 🤝 Katkıda Bulunma

### Geliştirme Süreci
1. **Fork** yapın
2. **Feature branch** oluşturun (`git checkout -b feature/amazing-feature`)
3. **Commit** yapın (`git commit -m 'Add some amazing feature'`)
4. **Push** edin (`git push origin feature/amazing-feature`)
5. **Pull Request** açın

### Kod Standartları
- **PSR-12** coding standards
- **Laravel conventions** takip edin
- **Vue 3 best practices** uygulayın
- **Type hints** kullanın
- **DocBlocks** yazın

### Commit Mesajları
```
feat: add user export functionality
fix: resolve permission checking issue
docs: update installation guide
style: format code with Pint
refactor: improve service layer structure
test: add user creation tests
```

## 📝 Changelog

### v1.0.0 (2025-01-XX)
- ✨ İlk stable release
- 🎉 Modüler mimari implementasyonu
- 🔐 Kapsamlı güvenlik özellikleri
- 📊 Admin panel ve dashboard
- 🎨 Modern UI/UX tasarımı

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakınız.

## 🙏 Teşekkürler

Bu proje aşağıdaki open source projeleri kullanmaktadır:
- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Inertia.js](https://inertiajs.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Jetstream](https://jetstream.laravel.com)

## 📞 İletişim

Sorular, öneriler veya geri bildirimler için:
- **Email**: [your-email@domain.com]
- **GitHub Issues**: [Repository Issues](https://github.com/your-repo/issues)
- **Documentation**: [Wiki](https://github.com/your-repo/wiki)

---

<p align="center">
Made with ❤️ using Laravel, Vue.js, and modern web technologies
</p>