# Docker y despliegue en GCP (Cloud Run)

Este documento describe la contenedorización de la aplicación y su despliegue en **Google Cloud Run** y servicios relacionados.

---

## 1. Imagen Docker

### 1.1 Características

- **Multi-stage**: construcción en 3 etapas (frontend Vite, Composer, runtime Nginx + PHP-FPM).
- **Runtime**: PHP 8.4-FPM Alpine + Nginx; escucha en **PORT** (por defecto **8080**, requerido por Cloud Run).
- **Seguridad**: workers de Nginx con usuario no root (`user app`).
- **Tamaño**: Alpine y `composer install --no-dev` para reducir la imagen final.

### 1.2 Construcción local

```bash
# Construir la imagen
docker build -t app-integraltech:latest .

# Ejecutar en local (puerto 8080)
docker run --rm -p 8080:8080 \
  -e APP_KEY="base64:..." \
  -e APP_ENV=production \
  -e DB_CONNECTION=sqlite \
  -e DB_DATABASE=/var/www/html/storage/database.sqlite \
  app-integraltech:latest
```

**Importante**: Antes del primer arranque en un entorno nuevo, generar `APP_KEY` con `php artisan key:generate` (en un contenedor temporal o en CI) y configurar las variables de entorno necesarias (base de datos, sesión, etc.).

### 1.3 Docker Compose (desarrollo / pruebas)

```bash
# Crear .env con APP_KEY y otras variables si hace falta
cp .env.example .env
# Editar .env y definir APP_KEY (o dejar el placeholder para pruebas)

# Construir y levantar
docker compose up --build

# App en http://localhost:8080
```

Los volúmenes `app_storage` y `app_bootstrap_cache` persisten SQLite y caché entre reinicios en local. En Cloud Run no se usan volúmenes; la app debe ser stateless.

---

## 2. Despliegue en GCP Cloud Run

### 2.1 Requisitos

- Cuenta en Google Cloud y proyecto creado.
- `gcloud` CLI instalado y autenticado (`gcloud auth login`, `gcloud config set project PROJECT_ID`).
- Docker (para construir y subir la imagen a Artifact Registry).

### 2.2 Configuración recomendada

| Aspecto | Recomendación |
|--------|----------------|
| **Puerto** | El contenedor debe escuchar en la variable **PORT** (Cloud Run la inyecta; por defecto 8080). El entrypoint ya sustituye `__PORT__` en la config de Nginx. |
| **Stateless** | No depender de disco local entre peticiones. Usar **Cloud SQL** (o similar) para BD y **Cloud Storage** para archivos si aplica. |
| **Sesiones** | Usar `SESSION_DRIVER=database` o Redis (Memorystore) en producción. |
| **Secrets** | Usar **Secret Manager** para `APP_KEY`, credenciales de BD, etc., y referenciarlos desde Cloud Run. |
| **Migraciones** | Ejecutar migraciones en **Cloud Build** (paso previo al deploy) o con un **Cloud Run Job**, no en el arranque de cada instancia. |

### 2.3 Build y deploy con Artifact Registry + Cloud Run

```bash
# 1. Habilitar APIs y crear repositorio (una vez por proyecto)
gcloud services enable run.googleapis.com artifactregistry.googleapis.com
gcloud artifacts repositories create app-repo --repository-format=docker --location=REGION

# 2. Configurar Docker para Artifact Registry
gcloud auth configure-docker REGION-docker.pkg.dev

# 3. Construir y subir la imagen
export REGION=us-central1
export PROJECT_ID=tu-proyecto
export IMAGE=REGION-docker.pkg.dev/$PROJECT_ID/app-repo/app-integraltech:latest

docker build -t $IMAGE .
docker push $IMAGE

# 4. Desplegar en Cloud Run
gcloud run deploy app-integraltech \
  --image $IMAGE \
  --region $REGION \
  --platform managed \
  --allow-unauthenticated \
  --set-env-vars "APP_ENV=production,APP_DEBUG=false" \
  --set-secrets "APP_KEY=app-key:latest" \
  --port 8080
```

Ajustar `REGION`, `PROJECT_ID`, `--set-env-vars` y `--set-secrets` según tu entorno. Para BD en Cloud SQL, añadir flags como `--add-cloudsql-instances=INSTANCE_CONNECTION_NAME` y variables de conexión.

### 2.4 Cloud Build (CI/CD)

Ejemplo de `cloudbuild.yaml` para construir, subir la imagen y desplegar:

```yaml
steps:
  - name: 'gcr.io/cloud-builders/docker'
    args: ['build', '-t', 'REGION-docker.pkg.dev/$PROJECT_ID/app-repo/app-integraltech:$SHORT_SHA', '.']

  - name: 'gcr.io/cloud-builders/docker'
    args: ['push', 'REGION-docker.pkg.dev/$PROJECT_ID/app-repo/app-integraltech:$SHORT_SHA']

  # Opcional: ejecutar migraciones con un Job o imagen temporal
  # - name: 'gcr.io/google.com/cloudsdktool/cloud-sdk'
  #   entrypoint: gcloud
  #   args: ['run', 'jobs', 'execute', 'migrate-job', ...]

  - name: 'gcr.io/google.com/cloudsdktool/cloud-sdk'
    entrypoint: gcloud
    args:
      - 'run'
      - 'deploy'
      - 'app-integraltech'
      - '--image=REGION-docker.pkg.dev/$PROJECT_ID/app-repo/app-integraltech:$SHORT_SHA'
      - '--region=REGION'
      - '--platform=managed'

images:
  - 'REGION-docker.pkg.dev/$PROJECT_ID/app-repo/app-integraltech:$SHORT_SHA'
```

Sustituir `REGION` y `PROJECT_ID` por los de tu proyecto.

---

## 3. Estructura de archivos Docker

| Archivo | Uso |
|--------|-----|
| **Dockerfile** | Build multi-stage: Node (Vite), Composer, Nginx + PHP-FPM. |
| **.dockerignore** | Excluye del contexto de build: `.git`, `node_modules`, `vendor`, tests, `.env`, etc. |
| **docker/entrypoint.sh** | Sustituye `__PORT__` en la config de Nginx y arranca PHP-FPM + Nginx. |
| **docker/nginx/nginx.conf** | Config principal de Nginx (user no root, include de http.d). |
| **docker/nginx/default.conf** | Bloque `server` para Laravel (root `public/`, PHP-FPM); contiene `__PORT__` para el entrypoint. |
| **docker-compose.yml** | Uso local: build + ejecución en puerto 8080 y volúmenes opcionales. |

---

## 4. Variables de entorno típicas en producción

Definir en Cloud Run (o Secret Manager) según necesidad:

- `APP_KEY` (obligatorio; desde Secret Manager recomendado).
- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://tu-dominio.run.app`.
- `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (por ejemplo Cloud SQL).
- `SESSION_DRIVER=database` (o `redis` si usas Memorystore).
- `CACHE_STORE`, `QUEUE_CONNECTION` (redis/database según arquitectura).
- `FILESYSTEM_DISK`, credenciales para GCS si usas almacenamiento en la nube.

---

## 5. Referencias

- [Cloud Run - Configuración de servicios](https://cloud.google.com/run/docs/configuring)
- [Cloud Run - Desarrollo](https://cloud.google.com/run/docs/developing)
- [Artifact Registry - Docker](https://cloud.google.com/artifact-registry/docs/docker/quickstart)
