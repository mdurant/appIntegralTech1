# App Servicios - Integraltech

**Documentación v0.1 (2026-01-24)**

**Líder de Desarrollo**: Mauricio Durán  
**Contacto**: `mauriciodurant@gmail.com`  
**Empresa**: IntegralTech - Soluciones Tech --  Chile

---

## Contexto del proyecto

Este repositorio implementa una **POC/MVP tipo Habitissimo**, orientada a:

- **Clientes** que publican solicitudes de servicios/obras (con formulario dinámico, contacto, ubicación e imágenes).
- **Usuarios/Proveedores** que visualizan solicitudes publicadas y envían **cotizaciones (presupuestos)** con vigencia.
- **Administración** (rol admin) con panel “light” para monitoreo y mantenedores básicos.

La aplicación usa autenticación con **Laravel Fortify** y UI con **Livewire v4 + Flux UI + Tailwind v4**.

---

## Tecnología aplicada

- **PHP**: 8.4.x  
- **Laravel**: 12.x  
- **Auth**: Fortify (login/register/reset password/2FA)
- **UI**: Livewire 4, Flux UI, Tailwind 4
- **DB**: SQLite (por defecto)
- **Queue**: database (por defecto)
- **Tests**: Pest
- **Permisos**: Spatie Laravel Permission (pendiente instalación)
- **Media**: Spatie Laravel Media Library (pendiente instalación)
- **PDF**: DomPDF (pendiente instalación)

---

## Setup inicial (clonar y correr)

### Requisitos
- PHP 8.4+
- Composer
- Node.js + npm

### Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
```

### Ejecutar en desarrollo

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Terminal 3 (recomendado si usarás cola `database`):

```bash
php artisan queue:work
```

### Tests

```bash
php artisan test --compact
```

---

## Credenciales para Login (seed)

El seeding crea usuarios base en `Database\\Seeders\\UserSeeder`:

- **Admin**
  - Email: `admin@integraltech.cl`
  - Password: `password`

- **Guest**
  - Email: `invitado@integraltech.cl`
  - Password: `password`

Además se crean:
- **Providers (users)**: 20 usuarios con nombres chilenos, emails chilenos y password `password`
- **Clients**: 3 usuarios con nombres chilenos, emails chilenos y password `password`

---

## Ciclo de vida de la POC (flujo principal)

### 1) Registro y validación por código (6 dígitos)
- Al registrarse, se genera un **código de 6 dígitos** con expiración (15 min) y se envía por correo (en entorno dev se loguea).
- El usuario debe validarlo en `verify-code` para acceder a `dashboard`.

### 2) Cliente: crear solicitud (draft) y publicar
- Ruta: `client/requests`
- Selecciona **Categoría → Subcategoría**
- Completa:
  - Trabajo a realizar
  - Nota del particular
  - Contacto (nombre/email/teléfono)
  - Ubicación (localización/dirección)
  - Formulario dinámico (según subcategoría)
  - Hasta **4 imágenes**
- Publica la solicitud → se habilita para recibir cotizaciones.

### 3) Proveedor: ver solicitudes publicadas y ofertar
- Listado: `services`
- Detalle: `services/{serviceRequest}`
- Envía cotización (monto + mensaje)
- La cotización queda con vigencia (`valid_until`) de **15 días**.

### 4) Cliente: gestionar cotizaciones
- Detalle: `client/requests/{serviceRequest}`
- Puede **reabrir** una solicitud vencida por 15 días adicionales.
- Puede **adjudicar** una cotización:
  - Se crea una **OT (Orden de Trabajo)**.
  - La solicitud pasa a estado `awarded`.

---

## Casos de uso reales (ejemplo Habitissimo)

Para la subcategoría **Construcción Casa**, la solicitud puede incluir:

1. Trabajo a realizar (texto)
2. Categoría/Subcategoría (tabla)
3. Plazo de realización (select dinámico)
4. Superficie aproximada (select dinámico)
5. Tipo de construcción (texto dinámico)
6. ¿Necesitas planos? (select dinámico)
7. Localización del trabajo (texto)
8. Nota del particular (texto)
9. Datos de contacto (nombre/teléfono/email)
10. Adjuntos: hasta 4 imágenes

---

## Esquema de BD (resumen) y relaciones

### Autenticación / Tenancy
- `users`
  - `system_role`: `admin | user | guest | client`
  - `current_tenant_id`: tenant activo (nullable)
  - 2FA: `two_factor_*`
- `tenants` (organización/cliente)
- `tenant_user` (pivot tenant-user con `role`)

Relaciones:
- `Tenant` **hasMany** `User` (many-to-many via `tenant_user`)
- `User` **belongsToMany** `Tenant` (via `tenant_user`)
- `User` **belongsTo** `Tenant` (current tenant)

### Verificación por código (6 dígitos)
- `email_verification_codes`
  - `code_hash` (no se guarda el código en texto plano)
  - `expires_at`, `consumed_at`

Relación:
- `EmailVerificationCode` **belongsTo** `User`

### Marketplace (solicitudes/cotizaciones/OT)
- `service_categories` (categoría/subcategoría, self `parent_id`)
- `service_form_fields` (preguntas por subcategoría)
- `service_form_field_options` (opciones para select)
- `service_requests` (solicitudes)
  - `category_id` apunta a una **subcategoría**
  - `expires_at` (ventana de publicación)
  - `awarded_bid_id` (nullable)
  - `contact_*`, `location_*`, `notes`
- `service_request_field_answers` (respuestas del formulario)
- `service_request_attachments` (adjuntos máx 4, validado en UI)
- `service_bids` (cotizaciones)
  - `valid_until` (vigencia 15 días)
- `work_orders` (OT)

Relaciones:
- `ServiceRequest` **belongsTo** `Tenant`, `ServiceCategory`, `User` (creator)
- `ServiceRequest` **hasMany** `ServiceBid`, `ServiceRequestFieldAnswer`, `ServiceRequestAttachment`
- `ServiceBid` **belongsTo** `ServiceRequest`, `User`
- `WorkOrder` **belongsTo** `ServiceRequest`, `ServiceBid`, `Tenant`, `User` (awarded_to)

---

## Rutas principales

- **Auth**
  - `login`, `register`, `forgot-password`, `reset-password`
  - `verify-code` (validación 6 dígitos)
- **Dashboard**
  - `dashboard`
  - `settings/*` (perfil/password/2FA)
- **Cliente**
  - `client/requests`
  - `client/requests/{serviceRequest}`
- **Proveedor**
  - `services`
  - `services/{serviceRequest}`
- **Admin**
  - `admin/`
  - `admin/service-categories`
  - `admin/service-form`

---

## Logs y correo (dev)

Por defecto el correo usa `MAIL_MAILER=log` (no envía emails reales).  
El envío del código 6 dígitos se registra en:

- Archivo: `storage/logs/email-codes-YYYY-MM-DD.log`

### Mostrar código completo en logs (sólo debug puntual)
En `.env`:

```bash
EMAIL_CODE_LOG_PLAINTEXT=true
```

Luego reinicia `php artisan serve` (y `queue:work` si aplica).

---

## Datos de Seeders (Chilenos)

Todos los seeders generan datos realistas chilenos:

- **Nombres**: Nombres y apellidos chilenos comunes
- **Teléfonos**: Formato `+569 xxxxxxxx` (celular chileno)
- **Emails**: Dominios chilenos (`gmail.com`, `hotmail.com`, `yahoo.cl`, `outlook.cl`, `live.cl`)
- **Localizaciones**: Regiones y comunas reales de Chile (16 regiones, todas las comunas)
- **Direcciones**: Direcciones chilenas con formato estándar
- **Empresas**: Nombres de empresas de fletes/transporte chilenos
- **Solicitudes**: Enfocadas en servicios de **fletes y transporte**
- **Categorías**: 
  - Fletes y Transporte (Fletes Urbanos, Interurbanos, Carga Pesada, Urgentes)
  - Mudanzas (Residenciales, Comerciales)
  - Logística (Distribución, Almacenamiento)

### Helper de Datos Chilenos

El proyecto incluye `App\Helpers\ChileanDataHelper` que proporciona métodos para generar:
- Teléfonos chilenos (`chileanPhone()`)
- Nombres chilenos (`chileanName()`)
- Emails chilenos (`chileanEmail()`)
- Localizaciones chilenas (`randomLocation()`)
- Direcciones chilenas (`chileanAddress()`)
- Nombres de empresas de fletes (`fleteCompanyName()`)
- Títulos y descripciones de solicitudes de fletes (`fleteRequestTitle()`, `fleteRequestDescription()`)
- Mensajes de cotización (`fleteBidMessage()`)

Los datos de regiones y comunas se basan en el JSON oficial de Chile disponible en: https://gist.github.com/juanbrujo/0fd2f4d126b3ce5a95a7dd1f28b3d8dd

---

## Arquitectura de Permisos

El proyecto está preparado para integrar **Spatie Laravel Permission** para gestión granular de permisos. La estructura base está lista:

### Instalación Pendiente

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Permisos Planificados

- **Cliente**: `view-own-requests`, `create-requests`, `manage-own-requests`
- **Profesional**: `view-published-requests`, `create-bids`, `manage-own-bids`
- **Admin**: `manage-all`, `configure-system`, `view-analytics`

### Seeder de Permisos

El seeder `RolePermissionSeeder` está creado y listo para poblar roles y permisos base una vez instalado el paquete.

---

## Sistema de Valoraciones

El sistema permite a los clientes valorar órdenes de trabajo completadas con:

- **Valoración**: 1-5 estrellas
- **Comentario**: Opcional, máximo 1000 caracteres
- **Validaciones**: Solo OTs completadas, una valoración por cliente por OT

### Modelo y Relaciones

- `ratings` table: `work_order_id`, `user_id`, `rating`, `comment`
- `Rating` **belongsTo** `WorkOrder`, `User`
- `WorkOrder` **hasMany** `Rating`

### Rutas

- `ratings/create/{workOrder}`: Formulario de valoración

---

## Dashboards Analíticos

### Dashboard Cliente (`client/dashboard`)

- **Métricas por estado**: Pendientes, En Curso, Completadas, Pagadas
- **Listado de OTs**: Con filtrado por estado
- **Gráfico de gastos por categoría**: Distribución de gastos (preparado para Chart.js)

### Dashboard Profesional (`provider/dashboard`)

- **Métricas de rendimiento**:
  - Total de cotizaciones enviadas
  - Cotizaciones aceptadas
  - Tasa de conversión (%)
  - Total de órdenes de trabajo
- **Análisis financiero**:
  - Total ofertado vs Total real (OTs completadas)
  - Gráfico comparativo (preparado para Chart.js)
- **Listado de OTs**: Órdenes de trabajo adjudicadas

### Configuración de Perfil Profesional (`provider/profile-settings`)

Permite a los profesionales completar:
- **RUT**: Validación con formato chileno
- **Giro SII**: Código de actividad económica
- **Logo/Imagen**: Upload de imagen de perfil

---

## Configuraciones del Sistema

El sistema incluye una tabla `system_settings` para parámetros configurables:

### Settings Disponibles

- `quote_validity_days`: Días de vigencia de cotizaciones (default: 15)
- `service_request_expiry_days`: Días de expiración de solicitudes (default: 15)
- `enable_ratings`: Habilitar sistema de valoraciones (default: true)
- `min_rating`: Valoración mínima (default: 1)
- `max_rating`: Valoración máxima (default: 5)

### Uso en Código

```php
use App\Models\SystemSetting;

// Obtener valor
$validityDays = SystemSetting::get('quote_validity_days', 15);

// Establecer valor
SystemSetting::set('quote_validity_days', 20, 'integer', 'Días de vigencia');
```

---

## Nuevas Tablas y Campos

### Tablas Nuevas

- `regions`: Regiones de Chile
- `communes`: Comunas de Chile (relacionadas con regiones)
- `banks`: Bancos activos en Chile
- `account_types`: Tipos de cuenta bancaria
- `ratings`: Valoraciones de órdenes de trabajo
- `system_settings`: Configuraciones del sistema

### Campos Nuevos

**`users`**:
- `rut`: RUT chileno (nullable)
- `giro_sii`: Giro SII (nullable)

**`service_requests`**:
- `region_id`: FK a `regions` (nullable)
- `commune_id`: FK a `communes` (nullable)

**`work_orders`**:
- `budget_estimated`: Presupuesto estimado del cliente (decimal)
- `final_price`: Precio final del profesional (decimal)
- `started_at`: Fecha de inicio (timestamp, nullable)
- `completed_at`: Fecha de finalización (timestamp, nullable)
- `paid_at`: Fecha de pago (timestamp, nullable)

### Estados Nuevos

**`ServiceBidStatus`**:
- `Draft`: Borrador
- `Expired`: Vencida (marcada automáticamente por comando)

**`WorkOrderStatus`**:
- `Paid`: Pagada

---

## Comandos Artisan

### Marcar Cotizaciones Vencidas

```bash
php artisan quotes:mark-expired
```

Este comando se ejecuta automáticamente diariamente vía scheduler. Marca como `Expired` todas las cotizaciones con `valid_until < now()` y estado `Submitted`.

---

## API Endpoints

### Búsqueda de Categorías (Select2)

```
GET /api/service-categories/search?q=texto
```

Retorna JSON compatible con Select2 para autocompletado de categorías en formularios.

---

## Sistema de Ofuscación de Datos y Pago Simulado

### Flujo de Visualización de Servicios Publicados

Cuando un cliente publica una solicitud de servicio, los usuarios (proveedores) solo pueden ver datos parciales hasta realizar un pago simulado:

#### 1. Vista de Listado (`services/browse`)
Los proveedores ven:
- **Título** y **Categoría** del servicio
- **Región + Comuna** (sin dirección específica)
- **Nombre ofuscado**: Solo el primer nombre del cliente (ej: "Juan" en lugar de "Juan Pérez González")
- **Teléfono ofuscado**: 3 primeros dígitos + "XXX XXX" (ej: "569 XXX XXX")
- **Email ofuscado**: 3 primeros caracteres + "xxxxx@xxxxxx.xxx" (ej: "jua xxxxx@xxxxxx.xxx")

#### 2. Modal "Ver Detalle"
Al hacer clic en "Ver detalle", se abre un modal con:
- **Panel Izquierdo**: Características completas del servicio (sin dirección específica, solo región+comuna)
- **Panel Derecho**: Sección "Contacta con tu potencial cliente" con:
  - Datos ofuscados (nombre, email, teléfono)
  - Precio para ver contacto completo ($6.105 CLP)
  - Botón "Ver contacto" que redirige al pago

#### 3. Pago Simulado (`services/{serviceRequest}/payment`)
Formulario de pago con:
- Titular de la tarjeta
- Número de tarjeta (16 dígitos)
- Fecha de expiración (mes/año)
- CVV (3-4 dígitos)
- Email
- Monto a pagar

**Nota**: Este es un pago simulado. No se procesa ningún cargo real. Los datos ingresados son solo para demostración.

#### 4. Vista de Contacto Completo (`services/{serviceRequest}/contact`)
Después del pago aprobado, el proveedor puede ver:
- Nombres completos del cliente
- Email completo
- Teléfono completo
- Dirección completa
- Idea del proyecto (descripción completa)
- Detalles adicionales del formulario
- Imágenes del proyecto
- Botones para contactar (email, teléfono)

### Modelo de Datos

**Tabla `payment_simulations`**:
- `user_id`: Usuario que realizó el pago
- `service_request_id`: Solicitud de servicio
- `amount`: Monto pagado
- `card_last_four`: Últimos 4 dígitos de la tarjeta
- `cardholder_name`: Nombre del titular
- `status`: Estado (pending, approved, rejected)
- `paid_at`: Fecha de pago

**Restricción**: Un usuario solo puede pagar una vez por solicitud (unique constraint).

### Rutas Relacionadas

- `services` - Listado de servicios con datos ofuscados
- `services/{serviceRequest}` - Detalle del servicio (datos según estado de pago)
- `services/{serviceRequest}/payment` - Formulario de pago simulado
- `services/{serviceRequest}/contact` - Datos completos después del pago

### Helpers y Utilidades

**`App\Helpers\DataObfuscationHelper`**:
- `obfuscateName(string $fullName): string` - Retorna solo el primer nombre
- `obfuscatePhone(string $phone): string` - Formato: "569 XXX XXX"
- `obfuscateEmail(string $email): string` - Formato: "jua xxxxx@xxxxxx.xxx"

**Accessors en `ServiceRequest`**:
- `obfuscated_contact_name` - Nombre ofuscado
- `obfuscated_phone` - Teléfono ofuscado
- `obfuscated_email` - Email ofuscado
- `location_display` - Región + Comuna (sin dirección)

---

## Notas
- Zona horaria usada localmente: `America/Santiago` (según tu config actual).
- Para cambios en UI (Vite), asegúrate de tener `npm run dev` ejecutándose.
- Todos los datos generados por seeders son en español y específicos de Chile.
- El sistema de pago es simulado y no procesa cargos reales.

