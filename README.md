# App Servicios - Integraltech

**Documentación técnica v0.2 (2026-01-30)**

**Líder de Desarrollo**: Mauricio Durán  
**Contacto**: `mauriciodurant@gmail.com`  
**Empresa**: IntegralTech - Soluciones Tech --  Chile

---

## Contexto del proyecto

Este repositorio implementa una **POC/MVP tipo Habitissimo**, orientada a:

- **Clientes** que publican solicitudes de servicios/obras (con formulario dinámico, contacto, ubicación e imágenes).
- **Usuarios/Proveedores** que visualizan solicitudes publicadas y envían **cotizaciones (presupuestos)** con vigencia.
- **Administración** (rol admin) con panel “light” para monitoreo y mantenedores básicos.
- **Suscripciones y API**: planes Freemium / Pro / Enterprise con acceso a API v1 (oportunidades, cotizaciones) y gestión de API Keys.

La aplicación usa autenticación con **Laravel Fortify** (web) y **Laravel Sanctum** (API), y UI con **Livewire v4 + Flux UI + Tailwind v4**.

---

## Tecnología aplicada

- **PHP**: 8.4.x  
- **Laravel**: 12.x  
- **Auth web**: Fortify (login/register/reset password/2FA)
- **Auth API**: Laravel Sanctum (tokens Bearer, API Keys)
- **UI**: Livewire 4, Flux UI, Tailwind 4
- **DB**: SQLite (por defecto)
- **Queue**: database (por defecto)
- **Tests**: Pest
- **Notificaciones**: Toaster (helper + componente Blade/Alpine; ver sección más abajo)
- **PDF**: DomPDF (barryvdh/laravel-dompdf)
- **API docs**: L5-Swagger (OpenAPI)
- **Permisos**: Spatie Laravel Permission (pendiente instalación)
- **Media**: Spatie Laravel Media Library (pendiente instalación)

---

## Theme / Design System (eco dashboard)

La aplicación usa un tema visual customizado inspirado en dashboards tipo “eco” (morado, teal, naranja), aplicado en **Landing** y **Dashboard** para una experiencia consistente.

### Paleta de colores

| Uso | Variable / Clase | Descripción |
|-----|------------------|-------------|
| **Primario / Marca** | `brand-50` … `brand-900` | Morado/violeta (oklch hue 275). Sidebar, botones primarios, enlaces, gráficos, acento Flux. |
| **Destacado / Activo** | `highlight-50` … `highlight-700` | Naranja/ámbar. Ítem activo del sidebar, CTAs secundarios, estados “activo”. |
| **Éxito / Positivo** | `success` | Teal/verde (oklch ~165). Indicadores positivos (+%), estados success, toasts de éxito. |
| **Advertencia** | `warning` | Ámbar. Alertas, toasts de advertencia. |
| **Error / Peligro** | `danger` | Rojo. Errores, toasts de error, validación. |
| **Informativo** | `info` | Azul. Mensajes informativos, toasts info. |

### Superficies y texto (Dashboard / Landing)

- **`app-bg`**: Fondo general (gris muy claro con tinte morado).
- **`app-surface`** / **`app-surface-2`**: Fondos de tarjetas y paneles.
- **`app-border`**: Bordes.
- **`app-text`**: Texto principal.
- **`app-muted`**: Texto secundario, hints.
- **`app-sidebar`** / **`app-sidebar-hover`**: Sidebar (claro por defecto).

### Tipografía

- **Fuente sans**: `Instrument Sans` (fallback: system-ui, sans-serif). Definida en `resources/css/app.css` vía `@theme { --font-sans: ... }`.

### Componentes

- **Calendarios (Flatpickr)**  
  Fechas seleccionadas: fondo `date-selected-bg`, borde `date-selected-border`, texto `date-selected-text` (tonos morado claro). Día actual: borde `brand-400` / `brand-600` si está seleccionado.

- **Formularios (Flux + Tailwind)**  
  Inputs, selects, textareas: borde `app-border`, focus con `ring-accent` (brand). Input groups (icono + campo): focus en grupo con `border-brand-500`. Select2: misma lógica de focus y opción resaltada con tonos `date-selected-*`.

- **Botones y enlaces**  
  Primarios: `variant="primary"` (accent = brand). Ghost/secondary: hover con `--color-hover-link` (brand-700). Footer: clase `footer-link` con hover morado.

- **Sidebar (Dashboard)**  
  Fondo claro (`app-sidebar`), colapsable solo en móvil. Ítem activo usa acento Flux (brand).

- **Toasts (notificaciones)**  
  Colores semánticos: success (teal), error (danger), warning (ámbar), info (azul). Posición: esquina superior derecha; componente `<x-toaster />` en el layout de app.

### Dónde se define

- **Variables y tema**: `resources/css/app.css` (bloque `@theme`).
- **Estilos de calendario**: mismas variables + clases `.flatpickr-*`.
- **Estilos de Select2**: `border-brand-*`, `date-selected-*` para opción seleccionada/resaltada.
- **Sidebar activo**: reglas `[data-flux-sidebar] [data-flux-sidebar-item][data-current]` en `app.css`.

Para cambios de marca (por ejemplo otro morado o otro naranja), basta con ajustar las variables `--color-brand-*` y `--color-highlight-*` en `@theme`.

---

## Toaster (notificaciones)

La aplicación usa un sistema propio de notificaciones tipo **toast** (sin dependencia externa) para feedback en todas las operaciones CRUD, perfil, envíos de correo y mensajes informativos, tanto en **Landing** como en **Dashboard** y pantallas de **auth**.

### Dónde está

- **Helper PHP**: `App\Helpers\Toaster` — métodos estáticos `success()`, `error()`, `warning()`, `info()` que flashean un mensaje en sesión para mostrarlo en la siguiente petición (útil tras `redirect()`).
- **Componente**: `<x-toaster />` — se incluye en el layout de app (`layouts/app/sidebar.blade.php`) y en el layout de auth (`layouts/auth.blade.php`). Renderiza toasts con Alpine.js (posición: esquina superior derecha; auto-cierre ~5 s).

### Uso

**Desde PHP (tras redirect):**

```php
use App\Helpers\Toaster;

Toaster::success(__('Guardado correctamente.'));
Toaster::error(__('No se pudo completar la acción.'));
Toaster::warning(__('Revisa los datos.'));
Toaster::info(__('Se ha enviado un correo a tu email.'));
// luego redirect()
```

**Desde Livewire (misma página, sin redirect):**

```php
$this->dispatch('toast', [['message' => __('Actualizado correctamente.'), 'type' => 'success']]);
// type: 'success' | 'error' | 'warning' | 'info'
```

**Convención:** En todo CRUD (admin, cliente, proveedor), perfil de usuario, cambio de contraseña, eliminación de cuenta, envíos de correo (verificación, etc.) y mensajes informativos se debe usar Toaster en lugar de `session()->flash('message')` o callouts inline, para una experiencia uniforme.

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

**Contraseña común para todos los usuarios de prueba:** `password`

### Usuarios base (UserSeeder)

- **Admin**: `admin@integraltech.cl`
- **Guest**: `invitado@integraltech.cl`
- **Providers (users)**: 20 usuarios con nombres/emails chilenos
- **Clients**: 3 usuarios con nombres/emails chilenos

### Usuarios de demostración Billing/API (BillingDemoSeeder)

| Perfil     | Email                     | Tier        | Uso |
|------------|---------------------------|-------------|-----|
| Freemium   | `freemium@integraltech.cl` | independent | Solo web; sin acceso API. En Pagos y Planes verás mensaje para actualizar. |
| Pro        | `pro@integraltech.cl`      | pyme        | API 60 req/min. Crear API keys, oportunidades, cotizaciones. |
| Enterprise | `enterprise@integraltech.cl` | enterprise  | API 500 req/min. Mismo acceso que Pro con mayor límite. |

Detalle de qué probar por perfil (suscripciones, payment placeholder, API, Swagger): **`docs/DATOS_PRUEBA_BILLING_API.md`**.

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
- Listado: `services` (muestra datos ofuscados: nombre parcial, teléfono XXX, email xxx)
- Modal "Ver detalle": Muestra características del servicio y datos ofuscados del cliente
- Pago simulado: `services/{serviceRequest}/payment` (para acceder a datos completos)
- Contacto completo: `services/{serviceRequest}/contact` (después del pago)
- Envía cotización (monto + mensaje)
- La cotización queda con vigencia (`valid_until`) de **15 días**.

### 4) Cliente: gestionar cotizaciones
- Detalle: `client/requests/{serviceRequest}`
- Puede **reabrir** una solicitud vencida por 15 días adicionales.
- Puede **adjudicar** una cotización:
  - Se crea una **OT (Orden de Trabajo)**.
  - La solicitud pasa a estado `awarded`.

### 5) Suscripciones y API (Pro / Enterprise)
- **Configuración → Pagos y Planes**: ver plan actual, historial de suscripciones, cambiar de plan (Upgrade/Cancelar), gestionar API Keys.
- **API Keys**: crear clave (Full Access o Solo lectura), copiar token (solo una vez), listar y revocar claves.
- **API v1** (con Bearer token): `GET /api/v1/opportunities`, `GET /api/v1/quotes`, `POST /api/v1/quotes`. Límites: Pro 60 req/min, Enterprise 500 req/min.
- **Documentación**: `/api/documentation` (Swagger).

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

### Autenticación / Tenancy / Suscripciones
- `users`
  - `system_role`: `admin | user | guest | client`
  - `current_tenant_id`: tenant activo (nullable)
  - `subscription_tier`: `independent | pyme | enterprise` (por defecto `independent`)
  - `subscription_ends_at`: fecha fin del plan (nullable)
  - 2FA: `two_factor_*`
- `tenants` (organización/cliente)
- `tenant_user` (pivot tenant-user con `role`)
- `personal_access_tokens` (Sanctum): API Keys por usuario (nombre, abilities: full_access / read_only)
- `subscriptions`: historial de suscripciones (user_id, subscription_tier, starts_at, ends_at, amount, currency, payment_method, external_id, metadata)

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
- `ServiceRequest` **belongsTo** `Tenant`, `ServiceCategory`, `User` (creator), `Region`, `Commune`
- `ServiceRequest` **hasMany** `ServiceBid`, `ServiceRequestFieldAnswer`, `ServiceRequestAttachment`, `PaymentSimulation`
- `ServiceBid` **belongsTo** `ServiceRequest`, `User`
- `WorkOrder` **belongsTo** `ServiceRequest`, `ServiceBid`, `Tenant`, `User` (awarded_to)
- `WorkOrder` **hasMany** `Rating`
- `PaymentSimulation` **belongsTo** `User`, `ServiceRequest`

---

## Dashboard Principal

El dashboard principal (`dashboard`) incluye:

### Visualización de Datos

- **Perfil de Usuario**: Visible en la barra superior derecha (desktop) con avatar, nombre y email
- **3 Gráficos Principales**:
  - **Usuarios Registrados**: Total de usuarios en el sistema
  - **Servicios Solicitados (Cotizaciones)**: Total de solicitudes publicadas
  - **Órdenes de Trabajo Generadas**: Total de OTs creadas
  - *Nota: Los gráficos están preparados para integración con Chart.js*

### Tarjetas de Estadísticas

- **Precios de Cotizaciones**: Muestra el precio promedio de las cotizaciones informadas (ej: $50.400)
- **Venta Estimada**: Muestra el valor acumulado de cotizaciones (ej: $18.400.000)
- **Cotizaciones Aceptadas**: Total de cotizaciones aceptadas
- **Tasa de Conversión**: Porcentaje de conversión de cotizaciones a OTs

### Cápsulas de Configuración

Tres cápsulas con íconos descriptivos SVG:
- **Perfil**: Ícono de usuario - Actualiza nombre, email y avatar
- **Seguridad**: Ícono de escudo - Cambia contraseña y protege cuenta
- **2FA**: Ícono de candado - Activa 2FA con QR y guarda códigos

---

## Rutas principales

- **Auth**
  - `login`, `register`, `forgot-password`, `reset-password`
  - `verify-code` (validación 6 dígitos)
- **Dashboard**
  - `dashboard` - Dashboard principal con gráficos y estadísticas
  - `settings/profile` - Ficha personal
  - `settings/password` - Contraseña
  - `settings/billing` - Pagos y Planes (plan actual, historial de suscripciones, API Keys)
  - `settings/two-factor` - 2FA
  - `settings/active-sessions` - Sesiones activas
  - `settings/appearance` - Apariencia
- **Cliente**
  - `client/requests`
  - `client/requests/{serviceRequest}`
- **Proveedor**
  - `services` - Listado con datos ofuscados
  - `services/{serviceRequest}` - Detalle (datos según estado de pago)
  - `services/{serviceRequest}/payment` - Pago simulado para ver contacto
  - `services/{serviceRequest}/contact` - Datos completos después del pago
  - `provider/work-orders` - Mis órdenes de trabajo
  - `provider/work-orders-chart` - Gráficos de OTs
  - `provider/bids` - Mis cotizaciones
  - `provider/dashboard` - Dashboard profesional
  - `provider/profile-settings` - Configuración de perfil profesional
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
- `payment_simulations`: Pagos simulados para acceder a datos de contacto completos

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

### Búsqueda de Categorías (Select2) — público

```
GET /api/service-categories/search?q=texto
```

Retorna JSON compatible con Select2 para autocompletado de categorías en formularios.

### API v1 (Sanctum, solo planes Pro y Enterprise)

Autenticación: `Authorization: Bearer {token}`. El token es una API Key creada en **Configuración → Pagos y Planes**.

| Método | Ruta | Descripción | Rate limit |
|--------|------|--------------|------------|
| GET | `/api/v1/opportunities` | Listar oportunidades publicadas (filtros: category_id, region_id, per_page) | Pro 60/min, Enterprise 500/min |
| GET | `/api/v1/quotes` | Listar mis cotizaciones | idem |
| POST | `/api/v1/quotes` | Crear cotización (body: service_request_id, amount, currency, message, valid_until) | idem |

- **Freemium**: responde **403** (requiere plan Pro o Enterprise).
- **Pro**: 60 solicitudes/minuto; **Enterprise**: 500 solicitudes/minuto. Superar el límite devuelve **429**.

### Documentación OpenAPI (Swagger)

- **Ruta**: `/api/documentation`
- **URL local** (con `php artisan serve`): **http://127.0.0.1:8000/api/documentation**
- **URL local** (puerto 80): **http://localhost/api/documentation**
- Regenerar docs tras cambiar anotaciones: `php artisan l5-swagger:generate`

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

## Sistema de Consentimiento de Cookies

El sistema implementa un modal de consentimiento de cookies que aparece automáticamente cuando un usuario visita el sitio por primera vez (tanto en la landing page como en los dashboards autenticados).

### Funcionalidad

El modal de cookies permite a los usuarios:
- **Aceptar todas las cookies**: Habilita cookies de Marketing y Experiencia de Usuario
- **Rechazar todas**: Solo permite cookies esenciales (necesarias para el funcionamiento del sitio)
- **Configuración de cookies**: Permite personalizar qué tipos de cookies aceptar

### Tipos de Cookies

#### Cookies de Marketing
Se activan cuando el usuario acepta las cookies de marketing. Almacenan:
- `marketing_visitor_id`: ID único del visitante
- `marketing_session_id`: ID de sesión actual
- `marketing_last_visit`: Fecha/hora de última visita
- `marketing_preferences`: Preferencias genéricas del usuario (fuente, user agent, fecha de aceptación)

**Propósito**: Mejorar la navegación del sitio, analizar el uso del mismo y apoyar iniciativas de marketing.

#### Cookies de Experiencia de Usuario
Se activan cuando el usuario acepta las cookies de experiencia de usuario. Almacenan:
- `ux_language`: Idioma preferido del usuario
- `ux_theme`: Tema preferido (light/dark)
- `ux_preferences`: Preferencias de navegación (JSON)
- `ux_last_activity`: Última actividad registrada

**Propósito**: Conocer al usuario visitante y entregar mejor experiencia personalizada.

#### Cookies Esenciales
Siempre activas, no requieren consentimiento:
- Cookies de sesión de Laravel
- Cookies de autenticación
- Cookies de CSRF

### Implementación Técnica

#### Componentes
- **`App\Services\CookieConsentService`**: Servicio para gestionar cookies y preferencias
- **`App\Livewire\CookieConsentModal`**: Componente Livewire del modal
- **`resources/views/livewire/cookie-consent-modal.blade.php`**: Vista del modal

#### JavaScript
Funciones en `resources/js/app.js`:
- `setCookie(name, value, days)`: Establece una cookie
- `getCookie(name)`: Lee una cookie
- `hasCookieConsent()`: Verifica si existe consentimiento
- `setCookieConsent(preferences)`: Guarda preferencias y crea cookies según tipo
- `createMarketingCookies()`: Crea cookies de marketing
- `createUserExperienceCookies()`: Crea cookies de UX
- `removeMarketingCookies()`: Elimina cookies de marketing
- `removeUserExperienceCookies()`: Elimina cookies de UX

#### Persistencia
- Las preferencias se guardan en cookies del navegador (no en base de datos)
- Duración: 365 días
- El modal solo aparece si no existe la cookie `cookie_consent_given`

#### Integración
El modal está integrado en:
- `resources/views/layouts/marketing.blade.php` (Landing page)
- `resources/views/layouts/app/sidebar.blade.php` (Dashboards autenticados)

### Texto del Modal

El modal muestra el siguiente texto en español:

> "Al hacer clic en «Aceptar todas las cookies», usted acepta el almacenamiento de cookies en su dispositivo para mejorar la navegación del sitio, analizar el uso del mismo y ayudarnos en nuestras iniciativas de marketing."

### Cumplimiento y Privacidad

El sistema está diseñado para cumplir con:
- Regulaciones de privacidad (Ley de Protección de Datos Personales de Chile)
- Buenas prácticas de consentimiento de cookies
- Transparencia sobre el uso de cookies

Los usuarios pueden cambiar sus preferencias en cualquier momento eliminando las cookies y recargando la página.

---

## Suscripciones, Pagos y Planes y API Keys

### Planes (tiers)

| Tier (DB)   | Plan (UX)              | API              | Rate limit   |
|-------------|------------------------|------------------|--------------|
| independent | Freemium (solo web)    | Sin acceso (403) | —            |
| pyme        | Pro                    | Lectura + cotizaciones | 60 req/min  |
| enterprise  | Enterprise             | Igual que Pro    | 500 req/min  |

### Configuración → Pagos y Planes (`settings/billing`)

- **Plan actual**: texto según tier (Freemium / Pro / Enterprise), fecha de caducidad si aplica, botones Upgrade / Cancelar suscripción.
- **Métodos de pago**: placeholder "Próximamente" (sin lógica de pago real).
- **Historial de suscripciones**: tabla con plan, inicio, fin, monto, método de pago.
- **API Keys** (solo Pro/Enterprise): crear clave (tipo Full Access / Solo lectura, nombre), listar claves (nombre, tipo, fecha), revocar. El valor del token **solo se muestra una vez** al crear.

### Cambio de plan (MVP)

- Upgrade: elegir Pro o Enterprise → confirmar → se actualiza `users.subscription_tier` y `subscription_ends_at`, y se crea registro en `subscriptions` (sin pasarela real).
- Cancelar suscripción: vuelve a Freemium (independent) y limpia `subscription_ends_at`.

---

## Aspectos importantes a considerar

- **Verificación por código**: Tras registrarse, el usuario debe validar con un código de 6 dígitos enviado por correo (en dev se loguea en `storage/logs/email-codes-*.log`).
- **API**: Solo usuarios con plan Pro o Enterprise pueden usar la API v1; Freemium recibe 403. Los rate limits son por usuario/token.
- **API Keys**: El token en texto plano solo se devuelve una vez al crear la clave; después no es recuperable. Usar tipo Full Access para enviar cotizaciones (POST); Solo lectura solo permite GET.
- **Pago real**: El flujo de "Pagos y Planes" y métodos de pago es placeholder; la monetización de la API (Pro/Enterprise) está preparada para integrar pasarela (Stripe, Mercado Pago, etc.) más adelante.
- **PDF de solicitudes**: Si la solicitud no tiene `pdf_path`, el controlador genera el PDF on-the-fly con DomPDF y lo guarda.
- **Datos chilenos**: Seeders y helpers generan datos realistas chilenos (RUT, regiones/comunas, nombres, etc.). Zona horaria por defecto: `America/Santiago`.
- **Tests**: Ejecutar `php artisan test --compact` antes de integrar cambios. Los tests de Billing, API y suscripciones están en `tests/Feature/BillingSettingsTest.php` y relacionados.

---

## Notas
- Zona horaria usada localmente: `America/Santiago` (según tu config actual).
- Para cambios en UI (Vite), asegúrate de tener `npm run dev` ejecutándose.
- Todos los datos generados por seeders son en español y específicos de Chile.
- El sistema de pago es simulado y no procesa cargos reales.

