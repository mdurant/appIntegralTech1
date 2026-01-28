# Instalación de Paquetes Spatie

Este documento describe los pasos para completar la instalación de los paquetes Spatie después de ejecutar `composer require`.

## Paquetes Requeridos

1. `spatie/laravel-permission` - Sistema de permisos
2. `spatie/laravel-medialibrary` - Gestión de medios
3. `barryvdh/laravel-dompdf` - Generación de PDFs

## Pasos de Instalación

### 1. Instalar Paquetes

```bash
composer require spatie/laravel-permission spatie/laravel-medialibrary barryvdh/laravel-dompdf
```

### 2. Publicar Migraciones y Configuración

#### Spatie Permissions
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

#### Spatie Media Library
```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config"
```

#### Laravel DomPDF
No requiere publicación de configuración.

### 3. Actualizar Modelo User

Descomentar las líneas en `app/Models/User.php`:

```php
// Descomentar estas líneas:
use \Spatie\Permission\Traits\HasRoles;
use \Spatie\MediaLibrary\HasMedia;
use \Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, HasMedia, InteractsWithMedia;
    
    // ... resto del código
}
```

### 4. Configurar Media Library en User

Agregar método `registerMediaCollections()` en el modelo User:

```php
public function registerMediaCollections(): void
{
    $this->addMediaCollection('logo')
        ->singleFile()
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
}
```

### 5. Ejecutar Seeder de Roles y Permisos

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 6. Ejecutar Seeder de System Settings

```bash
php artisan db:seed --class=SystemSettingSeeder
```

### 7. Verificar Instalación

- Verificar que las tablas `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `media` existen
- Verificar que el modelo User tiene los traits activos
- Probar generación de PDF de cotización
- Probar subida de logo con Media Library

## Notas

- El código ya está preparado para funcionar con o sin estos paquetes instalados
- Las Policies verifican la existencia de métodos antes de usarlos
- UserProfileService maneja ambos casos (con y sin Media Library)
