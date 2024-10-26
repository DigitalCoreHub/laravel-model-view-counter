# Model View Counter

Model View Counter, Laravel modellerinizin görüntülenme sayılarını takip etmenizi sağlayan ve cache yapısı kullanarak performansı optimize eden bir pakettir. Bu paket sayesinde, herhangi bir modelin kaç kez görüntülendiğini kolayca takip edebilir ve analiz edebilirsiniz.

## Özellikler

- **Model Bazlı Görüntülenme Sayacı**: Herhangi bir Laravel modelinin görüntülenme sayısını takip edin.
- **Cache Desteği**: Görüntülenme sayıları cache’de tutularak performans artırılır.
- **Kolay Entegrasyon**: Modellerinize basit bir trait ekleyerek hızlıca kullanmaya başlayın.
- **Event ve Listener**: Laravel’in event sistemi kullanılarak esnek bir yapı sunulur.
- **Cache Temizleme Komutu**: Cache’i temizlemek için Artisan komutu içerir.

## Kurulum

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/DigitalCoreHub/laravel-model-view-counter.git"
    }
],
```

```bash
composer require digitalcorehub/laravel-model-view-counter
php artisan vendor:publish
php artisan migrate
```

## Yapılandırma

`config/model-view-counter.php` dosyasında paketinizin ayarlarını yapılandırabilirsiniz:

```php
return [
    'models' => [
        /*
            Örnek:
            App\Models\User::class,
            App\Models\Blog::class,
        */
    ],
    'cache_enabled' => true, // Cache özelliğini etkinleştirmek için
    'cache_threshold' => 10, // Cache'de birikmesi gereken minimum sayı
    'cache_key' => 'model_view_counts', // Cache anahtarı
];
```

Kullanmak istediğiniz modelleri tanımlamayı unutmayın.

## Kullanım

### Modellerinize Trait’i Ekleyin

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DigitalCoreHub\LaravelModelViewCounter\Traits\CountableView;

class User extends Authenticatable
{
    use CountableView;
}
```

### Tetikleyin

```php
// Modeliniz görüntülendiğinde ModelViewed event’ini tetikleyin.
Route::get('users/{user:id}', function (User $user) {
    event(new ModelViewed($user));
});
```

### Görüntülenme Sayısını Çekin

```html
<!-- Görüntülenme sayısını çekin -->
<h1>{{ $user->name }}</h1>
<p>Görüntülenme Sayısı: {{ $user->viewCount() }}</p>
```

### Görüntülenme Sayısını Arttırın

```php
$user->incrementViewCount();
```