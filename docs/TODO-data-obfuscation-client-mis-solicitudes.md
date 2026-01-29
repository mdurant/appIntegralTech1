# To-Do: Rama data-obfuscation-and-payment-simulation

## Contexto

- **Flujo de simulación de pago**: solo perfiles "usuarios" (providers / `SystemRole::User`) pueden acceder al marketplace de servicios publicados, simular compra de datos ofuscados y ver datos de contacto legibles.
- **Perfil Cliente** (`SystemRole::Client`): no debe ver la opción "Servicios" (marketplace). Solo gestiona sus solicitudes antes de publicar en "Mis Solicitudes".

---

## Fase 1: Ocultar "Servicios" para perfil Cliente

| # | Tarea | Validación |
|---|--------|------------|
| 1.1 | Ocultar ítem "Servicios" del sidebar para usuarios con `isClient()` (mostrar solo para no-clientes: providers, admin). | Con usuario Cliente logueado: no aparece "Servicios" en sidebar. Con usuario Provider/Admin: sí aparece. |
| 1.2 | Ocultar ítem "Servicios" del header/navbar para clientes. | Misma validación en header superior. |
| 1.3 | Ocultar enlace "Explorar" (magnifying-glass) del header para clientes. | Icono de explorar solo visible para no-clientes. |
| 1.4 | Proteger rutas `services.browse`, `services.show`, `services.payment`, `services.contact`: redirigir o 403 si el usuario es Cliente. | Cliente que intente acceder por URL a `/services` recibe 403 o redirección a dashboard. |

---

## Fase 2: Mis Solicitudes – DataTable avanzado (solo Clientes)

| # | Tarea | Validación |
|---|--------|------------|
| 2.1 | Añadir buscador (por título, categoría, estado) con botón Buscar e icono lupa. | Búsqueda en tiempo real o al pulsar Buscar; resultados filtrados. |
| 2.2 | Paginación (ej. 20 por página) con estilos actuales (primary/hover #575eec). | Navegación entre páginas correcta y estilos aplicados. |
| 2.3 | Ordenamiento por columnas (título, categoría, estado, fechas). | Clic en cabecera ordena asc/desc. |
| 2.4 | Columnas visibles: al menos Título, Categoría/Subcategoría, Estado, Fecha creación, Acciones. Opcional: ícono PDF. | Tabla clara y legible. |
| 2.5 | Mostrar estado de cada solicitud con etiquetas claras: **draft**, **published**, **closed**, **awarded**, **cancelled**. Valorar estado derivado "vencida" (por `expires_at`) si aplica. | Estados visibles y traducidos/consistentes. |
| 2.6 | Acciones por fila: Ver, (Editar solo si draft), Publicar (solo draft), Eliminar. Iconos descriptivos. | CRUD coherente con reglas de negocio. |

---

## Fase 3: CRUD y flujo Cliente

| # | Tarea | Validación |
|---|--------|------------|
| 3.1 | Crear solicitud (draft): flujo actual mantenido; al guardar, además generar PDF (ver Fase 4). | Nueva solicitud creada y PDF generado. |
| 3.2 | Editar solicitud en estado draft (formulario pre-rellenado). | Solo drafts editables; cambios guardados. |
| 3.3 | Ver detalle (existente): sin cambios de lógica; enlace desde DataTable. | Ver lleva a detalle correcto. |
| 3.4 | Publicar (solo draft): sin cambios de lógica. | Estado pasa a published. |
| 3.5 | Eliminar: confirmación modal "¿Eliminar solicitud [título]?" antes de borrar. | Evita borrados accidentales. |

---

## Fase 4: PDF por solicitud (Cliente)

| # | Tarea | Validación |
|---|--------|------------|
| 4.1 | Al crear una solicitud (draft), generar un PDF asociado. Definir almacenamiento (ej. `storage/app/service-requests/{id}/solicitud.pdf` o en disco público con enlace). | Archivo PDF creado y guardado. |
| 4.2 | Contenido del PDF: nombre del cliente, dirección, fotos (incluidas o enlace), detalle del servicio (categoría, título, descripción), nota particular. Orden y formato claros. | PDF legible y completo. |
| 4.3 | Exponer ruta o controlador para ver/descargar el PDF (ej. `client/requests/{serviceRequest}/pdf`). Autorización: solo el tenant/cliente dueño de la solicitud. | Solo el cliente dueño puede abrir el PDF. |
| 4.4 | En la DataTable de "Mis Solicitudes", añadir columna o celda con ícono (ej. documento/PDF) que enlace a ver o descargar el PDF. | Un clic en el ícono abre/descarga el PDF. |

---

## Fase 5: Validación global y rama

| # | Tarea | Validación |
|---|--------|------------|
| 5.1 | Cambiar a rama `data-obfuscation-and-payment-simulation` y asegurar que no se pierden cambios de flujo de simulación de pago existente. | Merge/rebase correcto; tests pasan. |
| 5.2 | Probar flujo completo: Cliente crea solicitud → se genera PDF → aparece en DataTable con ícono → Ver/Editar/Publicar/Eliminar según estado. | Flujo sin errores. |
| 5.3 | Probar que usuario Provider/Admin sigue viendo "Servicios" y puede simular pago; Cliente no ve "Servicios" y no puede acceder por URL. | Permisos correctos por rol. |

---

## Estados de solicitud (referencia)

- **draft**: borrador; editable por cliente; puede publicar o eliminar.
- **published**: publicada; visible en marketplace; puede recibir cotizaciones.
- **closed**: cerrada sin adjudicación.
- **awarded**: adjudicada a un proveedor.
- **cancelled**: cancelada por el cliente/sistema.
- **vencida** (opcional): publicada pero `expires_at` ya pasó; puede mostrarse como etiqueta derivada.

---

## Orden sugerido de implementación

1. Fase 1 (ocultar Servicios a Cliente).
2. Fase 2 (DataTable Mis Solicitudes).
3. Fase 4 (generación y enlace del PDF).
4. Fase 3 (ajustes CRUD: editar draft, modal eliminar).
5. Fase 5 (validación global y rama).
