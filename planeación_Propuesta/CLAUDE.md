# CLAUDE.md — Sistema de Tutores Socráticos UNICAES

> Documento maestro de contexto para Claude Code. Define la arquitectura, lógica de negocio,
> modelo de datos, roles, seguridad y estándares de diseño del **Sistema de Gestión de Tutores
> Socráticos con IA** para la Universidad Católica de El Salvador (UNICAES).
> Todo el desarrollo debe respetar las reglas de este archivo.

---

## 1. Visión general del proyecto

Plataforma web centralizada que funciona como **catálogo institucional de tutores socráticos
basados en IA**. Cada tutor es un *agente socrático* asociado a una materia específica. La
plataforma **no ejecuta ni consume los agentes**: es el punto de acceso desde donde los
estudiantes **consultan la información de la materia, su plan de grado y descargan el agente**
para utilizarlo en la herramienta de IA que ellos elijan (ChatGPT, Claude, Gemini, etc.),
siguiendo los manuales de uso provistos.

La navegación se organiza jerárquicamente:

```
Facultad → Carrera → Ciclo → Materia → [Información + Plan de grado + Agente descargable]
```

El sistema es **extensible**: se pueden agregar nuevas facultades, carreras, ciclos y materias
sin modificación de código, mediante los módulos de administración.

### Objetivo del sistema
- Centralizar el acceso a los agentes socráticos por carrera, ciclo y materia.
- Permitir que los docentes suban los planes de grado de sus materias.
- Permitir que el administrador gestione todo el catálogo académico y suba los agentes.
- Garantizar acceso restringido exclusivamente a la comunidad UNICAES.

---

## 2. Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Arquitectura | **Monolito** (no microservicios) |
| Framework | **Laravel** (última versión LTS estable) |
| Lenguaje | PHP 8.3+ |
| Base de datos | MySQL 8 / MariaDB (PostgreSQL aceptable) |
| Autenticación | **Laravel Socialite** con **Google OAuth** |
| Frontend | Blade + Livewire (o Blade + Alpine.js). TailwindCSS para estilos |
| Assets | Vite |
| Almacenamiento archivos | `storage/` con disco privado (agentes y planes NO públicos directos) |
| Autorización | Policies + Gates nativos de Laravel + paquete de roles |
| Roles/Permisos | `spatie/laravel-permission` |
| Auditoría | `owen-it/laravel-auditing` (o equivalente) |

### Reglas del monolito
- Todo en un solo repositorio y una sola aplicación Laravel.
- Lógica de negocio en **Services** / **Actions**, no en los controladores.
- Controladores delgados; validación en **Form Requests**.
- Nada de lógica de negocio en las vistas Blade.

---

## 3. Autenticación y control de acceso

### 3.1 Login con Google (restringido por dominio)
- El **único** método de registro/login es **Google OAuth** vía Laravel Socialite.
- **Regla dura:** solo se permite el acceso a cuentas cuyo correo termine en
  **`@catolica.edu.sv`**. Cualquier otro dominio es rechazado en el callback,
  **antes** de crear el usuario.
- Validar el dominio en el callback del controlador de OAuth:
  ```php
  if (! str_ends_with($googleUser->getEmail(), '@catolica.edu.sv')) {
      // No crear usuario. Cerrar sesión. Redirigir con error.
      abort(403, 'Solo se permite el acceso con cuentas institucionales UNICAES.');
  }
  ```
- No debe existir registro manual con email/contraseña. No hay formulario de password.
- Al primer login válido, el usuario se crea con rol **estudiante** por defecto.
- Guardar `google_id`, `email`, `name`, `avatar`.

### 3.2 Roles del sistema (exactamente 3)

| Rol | Capacidades |
|-----|-------------|
| **estudiante** | Rol por defecto. Navega el catálogo (facultad→carrera→ciclo→materia). Visualiza información de la materia y el plan de grado. **Descarga** los agentes socráticos. Solo lectura/descarga. |
| **docente** | Todo lo del estudiante + **sube y gestiona los planes de grado** únicamente de las materias que tiene asignadas. Un docente puede estar asignado a **varias materias**. No puede subir agentes ni crear materias. |
| **admin** | Control total. Asigna roles a usuarios. Gestiona (CRUD) facultades, carreras, ciclos, materias, categorías. Crea materias y las asigna a un docente. **Sube y gestiona los agentes socráticos**. Administra planes de grado. Busca docentes. Ve la auditoría. |

- Los roles se gestionan con `spatie/laravel-permission`.
- **Solo el admin** puede cambiar el rol de un usuario (estudiante ↔ docente ↔ admin).
- Proteger cada acción con **Policies**. Nunca confiar solo en ocultar botones en la UI.

### 3.3 Matriz de permisos (resumen)

| Acción | Estudiante | Docente | Admin |
|--------|:---:|:---:|:---:|
| Ver catálogo y materias | ✅ | ✅ | ✅ |
| Descargar agente | ✅ | ✅ | ✅ |
| Ver plan de grado | ✅ | ✅ | ✅ |
| Subir plan de grado (solo materias asignadas) | ❌ | ✅ | ✅ |
| Subir/gestionar agentes | ❌ | ❌ | ✅ |
| Crear/editar materias | ❌ | ❌ | ✅ |
| Asignar materia a docente | ❌ | ❌ | ✅ |
| Crear categorías de materia | ❌ | ❌ | ✅ |
| Crear facultades / carreras / ciclos | ❌ | ❌ | ✅ |
| Asignar roles a usuarios | ❌ | ❌ | ✅ |
| Buscar docentes | ❌ | ❌ | ✅ |
| Ver auditoría | ❌ | ❌ | ✅ |

---

## 4. Modelo de datos

### 4.1 Entidades principales

**faculties** (facultades)
- `id`, `name`, `slug`, `description` (nullable), `is_active`, timestamps
- Una facultad tiene muchas carreras.

**careers** (carreras)
- `id`, `faculty_id` (FK), `name`, `slug`, `plan_year` (ej. "2021-2025", nullable), `is_active`, timestamps
- Pertenece a una facultad. Tiene muchas materias (a través de ciclos).

**cycles** (ciclos)
- `id`, `career_id` (FK), `number` (I, II, III, IV… o entero 1..N), `name` (nullable), timestamps
- Un ciclo pertenece a una carrera. Tiene muchas materias.
- Alternativa de diseño: si una materia puede repetirse en varias carreras/ciclos,
  usar tabla pivote (ver 4.2).

**subject_categories** (categorías de materia)
- `id`, `name` (ej. "Numérica", "Redacción", "Humanística", "Metodológica", "Programación"),
  `slug`, `color` (hex, opcional para badges), `description` (nullable), timestamps
- El admin puede **crear** nuevas categorías.
- Al crear una materia **se le debe asignar una categoría** (obligatorio).

**subjects** (materias)
- `id`, `name`, `slug`, `code` (código de materia, nullable), `subject_category_id` (FK, obligatorio),
  `description` (información de la materia), `is_active`, timestamps
- Cada materia pertenece a **una categoría**.
- Cada materia se asigna a **un docente** (ver relación 4.2) — el docente encargado del plan de grado.
- Una materia puede aparecer en varias carreras/ciclos (materias comunes compartidas).

**study_plans** (planes de grado)
- `id`, `subject_id` (FK), `uploaded_by` (FK users — docente o admin), `title`,
  `file_path` (disco privado), `file_name`, `file_size`, `mime_type`, `version`, `is_active`, timestamps
- Un plan de grado pertenece a una materia. Lo sube el docente asignado (o el admin).

**agents** (agentes socráticos / tutores)
- `id`, `subject_id` (FK), `uploaded_by` (FK users — admin), `name`, `description`,
  `file_path` (disco privado), `file_name`, `file_size`, `mime_type`, `version`,
  `download_count` (contador), `is_active`, timestamps
- **Cada agente está asociado a una materia.** Solo el admin lo sube.
- El archivo del agente es lo que el estudiante descarga (prompt/configuración del tutor socrático).

**users**
- `id`, `google_id`, `name`, `email` (único, dominio catolica.edu.sv), `avatar`,
  `email_verified_at`, timestamps
- Roles vía `spatie/laravel-permission` (tabla `model_has_roles`).

### 4.2 Relaciones clave

- **Docente ↔ Materias:** un docente (user con rol docente) está asignado a **varias materias**.
  - Si una materia tiene **un solo** docente encargado → FK `teacher_id` en `subjects`.
  - Recomendado: tabla pivote `subject_teacher` (`subject_id`, `user_id`) por flexibilidad,
    pero la regla de negocio dice "1 docente sube el plan": el docente encargado.
  - **Decisión:** `subjects.teacher_id` (FK a users) como docente encargado del plan de grado,
    ya que la responsabilidad del plan es de un único docente por materia.
    Un mismo docente puede ser `teacher_id` de muchas materias (cumple "varias materias").

- **Materia ↔ Carrera/Ciclo (materias compartidas entre carreras):**
  usar tabla pivote `career_subject`:
  - `career_subject`: `id`, `career_id` (FK), `cycle_id` (FK), `subject_id` (FK), timestamps
  - Permite que "Matemática I" exista en el Ciclo I de Ingeniería Industrial, Civil, Eléctrica, etc.
  - Esto refleja el requerimiento: **muchas carreras comparten materias**.

- **Materia → Categoría:** `subjects.subject_category_id` (FK, obligatorio).
- **Materia → Agente:** un agente por materia (o versionado; ver `agents.version`).
- **Materia → Plan de grado:** uno o varios planes (versionados) por materia.

### 4.3 Diagrama textual de relaciones

```
Faculty (1) ──< Career (N)
Career (1) ──< Cycle (N)
Career (N) >──< Subject (N)   [pivote career_subject, incluye cycle_id]
SubjectCategory (1) ──< Subject (N)
User[docente] (1) ──< Subject (N)        [teacher_id: docente encargado]
Subject (1) ──< StudyPlan (N)            [planes de grado, versionados]
Subject (1) ──< Agent (N)                [agentes socráticos, versionados]
User (N) >──< Role (N)                   [spatie: estudiante | docente | admin]
```

---

## 5. Módulos funcionales

### 5.1 Catálogo (estudiante)
- Pantalla de navegación por **Facultad → Carrera → Ciclo → Materia**.
- Buscador/filtros por facultad, carrera, categoría de materia.
- Vista de detalle de la materia:
  - Información de la materia (descripción, categoría, código).
  - **Plan de grado** disponible (si el docente lo subió) — visualizar/descargar.
  - **Agente socrático** disponible (si el admin lo subió) — botón de **descarga**.
  - Manual/instrucciones de uso del agente.
- Registrar cada descarga de agente (contador + auditoría).

### 5.2 Panel del docente
- Lista de **sus materias asignadas** únicamente.
- Subir / reemplazar / versionar el **plan de grado** de cada materia asignada.
- No ve ni gestiona materias que no le pertenecen.
- No puede subir agentes.

### 5.3 Panel de administración
- **Gestión de usuarios y roles:** buscar usuarios, asignar rol (estudiante/docente/admin).
- **Búsqueda de docentes:** buscador de usuarios con rol docente (para asignarlos a materias).
- **Facultades:** CRUD. Crear nuevas facultades.
- **Carreras:** CRUD. Cada carrera pertenece a una facultad.
- **Ciclos:** CRUD por carrera.
- **Categorías de materia:** CRUD. Crear categorías (numérica, redacción, humanística, etc.).
- **Materias:** CRUD. Al crear:
  1. Asignar **categoría** (obligatorio).
  2. Asignar a **un docente** (buscador de docentes).
  3. Asociar a carrera(s) y ciclo(s) vía pivote.
- **Agentes:** subir / versionar / activar-desactivar agentes por materia.
- **Planes de grado:** administrar (ver/eliminar) todos los planes.
- **Auditoría:** ver el registro de acciones (ver sección 6).

### 5.4 Flujo de creación de materia (regla de negocio central)
```
1. Admin crea (o selecciona) una CATEGORÍA de materia.
2. Admin crea la MATERIA → asigna categoría (obligatorio) + descripción.
3. Admin busca y asigna un DOCENTE encargado a la materia.
4. Admin asocia la materia a carrera(s) y ciclo(s).
5. El DOCENTE sube el PLAN DE GRADO de esa materia.
6. El ADMIN sube el AGENTE socrático de esa materia.
7. El ESTUDIANTE ya puede ver la materia, su plan y descargar el agente.
```

---

## 6. Sistema de auditoría

Objetivo: **controlar la gestión de documentos y registros** (planes de grado, agentes,
materias, roles, facultades, etc.).

- Usar `owen-it/laravel-auditing` sobre los modelos sensibles:
  `Agent`, `StudyPlan`, `Subject`, `SubjectCategory`, `Faculty`, `Career`, `Cycle`, `User` (roles).
- Registrar como mínimo: **quién** (user_id), **qué** (evento: created/updated/deleted),
  **cuándo** (timestamp), **valores anteriores y nuevos**, **IP** y **user-agent**.
- Eventos adicionales personalizados a registrar explícitamente:
  - Descarga de agente (`agent.downloaded`) con user + agente + materia.
  - Descarga/visualización de plan de grado.
  - Cambio de rol de un usuario (`role.assigned`) con actor y objetivo.
  - Subida/reemplazo de agente o plan (`agent.uploaded`, `study_plan.uploaded`).
- El panel de admin muestra la auditoría con filtros por usuario, modelo, evento y fecha.
- Los registros de auditoría son **inmutables** (no editables ni borrables desde la app).

---

## 7. Manejo de archivos (agentes y planes de grado)

- Almacenar en **disco privado** (`storage/app/private/...`), **nunca** en `public/`.
- La descarga se sirve a través de un **controlador protegido por Policy**, no por URL directa.
  ```php
  // El acceso al archivo pasa SIEMPRE por autorización
  Storage::disk('private')->download($agent->file_path, $agent->file_name);
  ```
- Validar tipo y tamaño en Form Requests (ej. agentes: `.txt`, `.md`, `.json`, `.pdf`;
  planes: `.pdf`, `.docx`). Definir límites de tamaño.
- Nombres de archivo saneados; almacenar con nombre generado (hash/uuid), guardar el nombre
  original en BD.
- Versionado: al reemplazar, conservar histórico (campo `version`, `is_active`).
- Escaneo/validación de archivos subidos antes de aceptarlos.

---

## 8. Paleta de colores UNICAES

> Extraída de la identidad visual institucional (sitio catolica.edu.sv, mallas curriculares
> y escudo). El **verde institucional** es el color primario de la marca académica; el **azul**
> es el color de tema del sitio web; el **granate y dorado** provienen del escudo.

### Tokens de color (definir en `tailwind.config.js` → `theme.extend.colors`)

```js
colors: {
  unicaes: {
    // Verde institucional (primario - mallas curriculares y branding académico)
    green:      '#3AAA35',   // primario
    'green-dark':'#2E8B2C',  // hover / estados activos
    'green-light':'#8CC63F', // acentos secundarios / badges

    // Azul institucional (color de tema del sitio web)
    blue:       '#0258FC',   // enlaces, acciones secundarias

    // Escudo — granate/vino
    maroon:     '#6B1F2A',   // encabezados solemnes, detalles institucionales
    // Escudo — dorado
    gold:       '#C9A227',   // acentos, medallas, distinciones

    // Neutros
    ink:        '#1F2933',   // texto principal
    slate:      '#52606D',   // texto secundario
    mist:       '#F5F7FA',   // fondos suaves
    line:       '#E4E7EB',   // bordes / divisores
  }
}
```

### Uso semántico
- **Primario / CTA principal:** `unicaes.green` (botones "Descargar agente", acciones clave).
- **Hover primario:** `unicaes.green-dark`.
- **Enlaces / acción secundaria:** `unicaes.blue`.
- **Badges de categoría de materia:** usar `subject_categories.color` o derivar de la paleta.
- **Encabezados institucionales / footer:** `unicaes.maroon` con acentos `unicaes.gold`.
- **Fondos de sección:** `unicaes.mist`; texto: `unicaes.ink` / `unicaes.slate`.
- Mantener contraste **AA (WCAG 2.1)** en todo texto sobre color.

> Nota: confirmar los hex exactos contra el manual de marca oficial de UNICAES si está
> disponible en la sección de "Descargables". Estos valores son fieles a la identidad visual
> pero deben validarse con el logo oficial descargable.

---

## 9. Skills de Claude Code a utilizar

Este proyecto debe apoyarse en las **skills instaladas en tu Claude Code**:

### 9.1 Skill de diseño (Apple Design)
- Usar la skill de **diseño estilo Apple** para toda la construcción de UI/UX:
  jerarquía visual clara, espaciado generoso, tipografía legible, componentes consistentes,
  microinteracciones sutiles, estados de foco accesibles.
- Aplicar la paleta UNICAES (sección 8) **dentro** de los principios de esa skill:
  limpieza, claridad y foco en el contenido.
- Toda pantalla nueva (catálogo, detalle de materia, paneles) debe pasar por esta skill
  antes de considerarse terminada.

### 9.2 Skill de seguridad (Security Check)
- Ejecutar la skill de **security check** sobre el sistema para **garantizar el funcionamiento
  completo y seguro** antes de cada entrega/merge importante.
- Verificar como mínimo:
  - Restricción de dominio `@catolica.edu.sv` en OAuth (no bypass).
  - Autorización por Policy en **cada** endpoint (no solo UI).
  - Descarga de archivos siempre autorizada (sin URLs públicas directas).
  - Protección CSRF, XSS (escape de Blade), SQL injection (Eloquent/bindings).
  - Validación de subida de archivos (tipo, tamaño, saneo de nombre).
  - Rate limiting en login y descargas.
  - Secrets fuera del repositorio (`.env`), nunca commiteados.
  - Auditoría activa e inmutable en modelos sensibles.
- No marcar una funcionalidad como completa hasta que la skill de seguridad pase sin hallazgos
  críticos.

> Si estas skills no están activas en la sesión, solicitarlas/activarlas antes de generar UI
> o de cerrar una funcionalidad con impacto de seguridad.

---

## 10. Convenciones de desarrollo

### 10.1 Estructura y estilo
- Seguir convenciones de Laravel (PSR-12).
- **Controladores delgados** → lógica en `app/Services` o `app/Actions`.
- **Validación** en Form Requests (`app/Http/Requests`).
- **Autorización** en Policies (`app/Policies`) — una policy por modelo sensible.
- Nombres de rutas con nombre (`route('subjects.show')`), agrupadas por rol/middleware.
- Migraciones con FKs, índices y `onDelete` explícitos.
- Seeders para: roles (estudiante/docente/admin), un admin inicial, facultades y categorías base.

### 10.2 Middleware y rutas
- Grupo `auth` para todo el sistema (nadie anónimo salvo login).
- Middleware de rol para paneles: `role:admin`, `role:docente`.
- Middleware personalizado que valide dominio institucional en cada request si aplica.

### 10.3 Testing
- **Feature tests** para los flujos críticos:
  - Login rechaza dominios no `@catolica.edu.sv`.
  - Estudiante NO puede subir plan ni agente (403).
  - Docente solo gestiona SUS materias (403 en materias ajenas).
  - Solo admin asigna roles / crea materias / sube agentes.
  - Descarga de archivo exige autorización.
- **Unit tests** para services/actions de negocio.
- Ejecutar `php artisan test` verde antes de cada entrega.

### 10.4 Datos semilla iniciales (seeders)
- Roles: `estudiante`, `docente`, `admin`.
- Facultad base: **Ingeniería y Arquitectura** (y las otras 4 facultades reales de UNICAES:
  Ciencias Empresariales, Ciencias y Humanidades, Ciencias de la Salud, Multidisciplinaria de Ilobasco).
- Categorías base de materia: Numérica, Redacción, Humanística, Metodológica, Programación,
  Ciencias Básicas de Ingeniería.
- Un usuario admin inicial (por email institucional) para arrancar la gestión.

---

## 11. Reglas de negocio invariantes (no romper)

1. **Solo** cuentas `@catolica.edu.sv` pueden entrar. Sin excepción.
2. Existen **exactamente 3 roles**: estudiante, docente, admin.
3. El rol por defecto de todo usuario nuevo es **estudiante**.
4. **Solo el admin** asigna roles y sube agentes.
5. **Solo el docente asignado** (o el admin) sube el plan de grado de una materia.
6. Un docente puede tener **varias materias** asignadas.
7. Toda materia **debe** tener una **categoría** asignada al crearse.
8. Toda materia se asigna a **un docente encargado**.
9. Cada **agente** está asociado a **una materia**.
10. Las carreras pertenecen a una **facultad**; se pueden crear más facultades.
11. Las materias comunes pueden **compartirse entre varias carreras/ciclos**.
12. Los estudiantes **descargan** agentes; la plataforma **no** ejecuta los agentes.
13. Toda gestión de documentos y registros queda **auditada** e inmutable.
14. Los archivos (agentes/planes) **nunca** son públicos; se sirven autorizados.

---

## 12. Roadmap sugerido de implementación

1. **Fase 0 — Setup:** Laravel, Tailwind con tokens UNICAES, Socialite Google, spatie/permission,
   laravel-auditing. Seeders de roles + admin inicial.
2. **Fase 1 — Auth:** login Google con restricción de dominio + asignación de rol por defecto.
   Feature tests de acceso. Pasar **security check**.
3. **Fase 2 — Estructura académica:** CRUD de facultades, carreras, ciclos, categorías, materias
   (con categoría obligatoria y docente asignado). Pivote career_subject.
4. **Fase 3 — Documentos:** subida de planes de grado (docente) y agentes (admin) en disco privado
   + descarga autorizada + versionado.
5. **Fase 4 — Catálogo estudiante:** navegación facultad→carrera→ciclo→materia, detalle, descarga.
   Aplicar **skill de diseño Apple** a todas las pantallas.
6. **Fase 5 — Auditoría y panel:** vista de auditoría, contadores de descarga, gestión de roles.
7. **Fase 6 — Hardening:** correr **security check** completo, tests verdes, revisión de accesibilidad
   y contraste de la paleta.

---

## 13. Notas finales para Claude Code

- Antes de escribir UI: **invocar la skill de diseño (Apple)**.
- Antes de cerrar cualquier funcionalidad sensible: **invocar la skill de security check**.
- Mantener el **monolito** — no proponer microservicios ni SPAs desacopladas.
- Cualquier archivo servido al usuario pasa por **autorización**; jamás enlaces públicos directos.
- Respetar la **paleta UNICAES** y validar contraste accesible.
- Ante ambigüedad en reglas de negocio, respetar las **invariantes de la sección 11**.
