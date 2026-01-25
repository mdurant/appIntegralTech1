# App Servicios - Integraltech

**Documentación v0.1 (2026-01-24)**

**Líder de Desarrollo**: Mauricio Durán  
**Contacto**: `Mauricioduant@gmail.com`  
**Empresa**: IntegralTech - Soluciones Tech. Chile

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
  - Email: `admin@example.com`
  - Password: `password`

- **Guest**
  - Email: `guest@example.com`
  - Password: `password`

Además se crean:
- **Providers (users)**: 20 usuarios (email aleatorio) con password `password`
- **Clients**: 3 usuarios (email aleatorio) con password `password`

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

## Notas
- Zona horaria usada localmente: `America/Santiago` (según tu config actual).
- Para cambios en UI (Vite), asegúrate de tener `npm run dev` ejecutándose.

