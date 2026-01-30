# Datos de prueba: Suscripciones, Pagos y Planes, API

## Cómo poblar los datos

Ejecuta el seeder completo (incluye `BillingDemoSeeder`):

```bash
php artisan db:seed
```

Solo usuarios de demostración de billing/API:

```bash
php artisan db:seed --class=BillingDemoSeeder
```

---

## Usuarios de prueba (credenciales)

**Contraseña común para todos:** `password`

| Perfil        | Email                     | Contraseña | Tier        | Uso |
|---------------|---------------------------|------------|-------------|-----|
| Freemium      | `freemium@integraltech.cl` | password   | independent | Sin API, solo web. En Pagos y Planes verás mensaje para actualizar a Pro/Enterprise. |
| Pro           | `pro@integraltech.cl`      | password   | pyme        | API 60 req/min. Crear API keys, listar oportunidades, enviar cotizaciones. |
| Enterprise    | `enterprise@integraltech.cl` | password | enterprise  | API 500 req/min. Mismo acceso que Pro con mayor límite. |

---

## Qué probar por perfil

### Modelo de suscripción

- **Configuración → Pagos y Planes**: plan actual (Freemium / Pro / Enterprise), fecha de caducidad si aplica.
- **Historial de suscripciones**: tabla con plan, inicio, fin, monto, método de pago (Pro y Enterprise tienen registros creados por el seeder).
- **Cambio de plan**: con Freemium, botones "Upgrade a Pro" / "Upgrade a Enterprise"; con Pro, "Upgrade a Enterprise" y "Cancelar suscripción". Confirmar y comprobar que se actualiza el plan y se crea registro en historial.
- **Cancelar suscripción**: con Pro o Enterprise, "Cancelar suscripción" deja el usuario en Freemium (independent).

### Payment (métodos de pago)

- En **Pagos y Planes** la sección "Métodos de pago" es placeholder: texto "Próximamente: tarjeta, PayPal y más." (sin lógica de pago real).

### API según perfil

- **Freemium (`freemium@integraltech.cl`)**: sin acceso a API. Cualquier llamada a `/api/v1/*` con su token debe responder **403** (requiere plan Pro o Enterprise).
- **Pro (`pro@integraltech.cl`)**:  
  1. Iniciar sesión en la web.  
  2. Ir a **Configuración → Pagos y Planes** y crear una API key (Full Access o Solo lectura).  
  3. Usar el token en `Authorization: Bearer {token}`.  
  4. **GET** `/api/v1/opportunities` (listar oportunidades).  
  5. **GET** `/api/v1/quotes` (listar mis cotizaciones).  
  6. **POST** `/api/v1/quotes` (crear cotización; solo con clave Full Access).  
  7. Límite: **60 solicitudes/minuto** (respuesta **429** si se supera).
- **Enterprise (`enterprise@integraltech.cl`)**: mismo flujo que Pro; límite **500 solicitudes/minuto**.

### API Keys en la UI

- Con **Pro** o **Enterprise**: en Pagos y Planes ver bloque "API Keys": crear clave (tipo + nombre), ver lista (nombre, tipo, fecha), revocar. El valor del token solo se muestra una vez al crear.
- Con **Freemium**: mensaje "Actualiza a Pro o Enterprise para usar la API".

---

## URL de Swagger (documentación OpenAPI)

La documentación de la API se sirve con L5-Swagger en la ruta:

**Ruta relativa:** `/api/documentation`

**URLs típicas en local:**

- Con `php artisan serve`:  
  **http://127.0.0.1:8000/api/documentation**
- Con servidor en puerto 80 (o dominio local):  
  **http://localhost/api/documentation**
- Si usas `APP_URL` en `.env` (ej. `http://localhost:8080`):  
  **{APP_URL}/api/documentation**  
  Ejemplo: **http://localhost:8080/api/documentation**

Generar/regenerar la documentación (tras cambiar anotaciones OpenAPI):

```bash
php artisan l5-swagger:generate
```

En la UI de Swagger puedes marcar **Authorize** e introducir el Bearer token (API key creada para un usuario Pro o Enterprise) para probar los endpoints protegidos.
