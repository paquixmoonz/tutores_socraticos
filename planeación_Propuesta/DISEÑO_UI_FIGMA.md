# Brief de diseño UI/UX — Sistema de Tutores Socráticos UNICAES

> Documento para Figma Make. Describe el producto, la identidad visual y cada pantalla
> con su layout, componentes, estados y prioridades de experiencia. El objetivo es un
> diseño limpio tipo Apple (jerarquía clara, espacio generoso, foco en el contenido)
> con la identidad institucional de UNICAES.

---

## 1. Qué es el producto (contexto para diseñar)

Plataforma web universitaria donde:

- El **estudiante** navega `Facultad → Carrera → Ciclo → Materia` y en la materia
  **descarga o copia un "agente socrático"** (un prompt de IA) para usarlo en ChatGPT,
  Claude o Gemini. También descarga el plan de grado.
- El **docente** sube y versiona el plan de grado de sus materias asignadas.
- El **admin** gestiona todo el catálogo, los roles y ve la auditoría.

**La acción más importante de todo el sistema:** que el estudiante llegue a su materia
y presione "Descargar agente" o "Copiar al portapapeles" en el menor número de clics.
Todo lo demás es secundario a ese flujo.

**Usuarios:** estudiantes universitarios (móvil primero), docentes (móvil y escritorio),
administradores (escritorio). Idioma: español. Tono: institucional pero cercano.

---

## 2. Identidad visual (tokens obligatorios)

### Colores

| Token | Hex | Uso |
|---|---|---|
| `verde` (primario) | `#3AAA35` | CTA principal ("Descargar agente"), estados activos, éxito |
| `verde-oscuro` | `#2E8B2C` | Hover del primario |
| `verde-claro` | `#8CC63F` | Acentos secundarios, badges suaves |
| `azul` | `#0258FC` | Enlaces, acciones secundarias, plan de grado |
| `granate` | `#6B1F2A` | Marca, encabezado del logo, footer, detalles solemnes |
| `dorado` | `#C9A227` | Acentos institucionales, subtítulo "UNICAES", distinciones |
| `tinta` | `#1F2933` | Texto principal |
| `pizarra` | `#52606D` | Texto secundario |
| `niebla` | `#F5F7FA` | Fondo general de la app |
| `línea` | `#E4E7EB` | Bordes y divisores |

Reglas: fondo general `niebla`, tarjetas blancas con borde `línea` y sombra muy sutil,
radios generosos (12–16 px), contraste AA en todo texto sobre color. El granate y el
dorado se usan con moderación (marca y footer), nunca como colores de acción.

### Tipografía

Sans-serif del sistema (SF Pro / Segoe UI / Inter). Títulos en bold con tracking
ligeramente negativo; cuerpo 14–16 px con interlineado cómodo; etiquetas y metadatos
en 11–12 px `pizarra`. Jerarquía por peso y tamaño, no por color.

### Componentes base (design system mínimo)

- **Botón primario:** verde, texto blanco semibold, radio 12 px, icono opcional a la izquierda.
- **Botón secundario:** blanco, borde `línea`, texto `tinta`.
- **Tarjeta:** blanca, borde `línea`, radio 16 px, padding generoso, hover con sombra suave.
- **Badge de categoría:** píldora con el color propio de la categoría al 10 % de opacidad
  de fondo y el color pleno en el texto (ej. "Programación" en verde, "Numérica" en azul).
- **Badge de estado:** "Activa/Activo" (verde suave), "Histórica/Inactiva" (gris), "Pendiente" (ámbar).
- **Campos de formulario:** radio 12 px, borde `línea`, foco con anillo verde suave.
- **Tabla:** encabezado gris claro con texto uppercase 11 px, filas divididas por `línea`,
  en móvil se colapsa a tarjetas o scroll horizontal.
- **Flash de éxito:** franja verde suave con auto-cierre; errores en franja roja suave con lista.
- **Breadcrumb:** texto pequeño `pizarra` con separador "/", último elemento en `tinta`.

### Navegación global

Header pegajoso translúcido (blur) con: logo (cuadro verde con "T" + "Tutores Socráticos /
UNICAES" en granate y dorado), enlaces según rol (Catálogo · Mis materias · Administración),
avatar + nombre + rol, botón "Salir". En móvil: menú hamburguesa. Footer granate con el
nombre de la universidad.

---

## 3. Pantallas

### P1 — Login (pública)

**Objetivo:** entrar con un solo clic. Cero fricción, cero formularios de contraseña.

- Centrado vertical sobre fondo `niebla`. Tarjeta única de ~420 px.
- Arriba: logo grande (cuadro verde "T"), "Tutores Socráticos" en granate,
  "UNICAES" en dorado uppercase con tracking amplio.
- Tarjeta: saludo breve ("Accede con tu cuenta institucional…"), **un solo botón**
  "Continuar con Google" con el logo de Google, y debajo una nota con icono de escudo:
  "Solo cuentas @catolica.edu.sv".
- Estado de error (dominio rechazado): franja roja suave arriba del botón con el mensaje.
- No hay registro, ni "olvidé mi contraseña", ni enlaces extra. Minimalismo total.

### P2 — Catálogo / inicio del estudiante

**Objetivo:** orientarse en 2 segundos: buscar directo o navegar por facultad.

- Título "Catálogo de tutores socráticos" + subtítulo de una línea explicando el flujo.
- **Buscador prominente** (max 600 px): input grande "Busca una materia por nombre o código…" + botón verde.
- Resultados de búsqueda (si hay query): grid de tarjetas de materia — nombre, badge de
  categoría, código, e indicador "● Agente disponible" (verde) / "○ Agente aún no disponible" (gris).
  Estado vacío: tarjeta con mensaje amable y sugerencia de navegar por facultad.
- Sección "Facultades": grid responsive (3 col escritorio / 2 tablet / 1 móvil) de
  tarjetas con inicial en un cuadro granate suave, nombre de la facultad y contador de carreras.

### P3 — Facultad (lista de carreras)

- Breadcrumb: Catálogo / Facultad.
- Título de la facultad + descripción opcional.
- Grid de tarjetas de carrera: nombre, "Plan 2021-2025", contador de materias.
- Estado vacío: "Esta facultad aún no tiene carreras registradas."

### P4 — Carrera (materias por ciclo)

**Objetivo:** escanear rápido el ciclo propio.

- Breadcrumb completo. Título de carrera + facultad y año de plan como subtítulo.
- Secciones por ciclo: encabezado con chip verde del número romano ("I", "II") + "Ciclo I"
  + contador. Debajo, grid de tarjetas de materia.
- **Tarjeta de materia** (componente clave, reutilizada en todo el sistema):
  nombre, badge de categoría con color, código, y fila inferior con dos indicadores
  de disponibilidad: "● Agente" (verde) y "● Plan de grado" (azul), en gris con "○" si faltan.
- Ciclo sin materias: franja punteada "Sin materias en este ciclo todavía."

### P5 — Detalle de materia ⭐ (la pantalla más importante)

**Objetivo:** descargar o copiar el agente sin pensar. Layout de 2 columnas en
escritorio (contenido 2/3 + panel de acción 1/3); en móvil el panel de acción va
inmediatamente después del encabezado.

Columna principal:
- Encabezado: nombre de la materia (título grande), badge de categoría, línea de
  metadatos (código · docente encargado).
- "Sobre la materia": descripción.
- "Se imparte en": chips grises con carrera + ciclo.
- **Tarjeta "¿Cómo usar tu tutor socrático?"**: 4 pasos numerados con círculos verdes
  (1 descargar/copiar → 2 abrir tu IA → 3 pegar como primer mensaje → 4 preguntar).

Panel lateral (sticky en escritorio):
- **Tarjeta "Agente socrático"** con cabecera verde suave: nombre del agente, descripción,
  metadatos (versión · tamaño · descargas) y DOS botones apilados:
  **"Descargar agente"** (primario verde, icono descarga) y
  **"Copiar al portapapeles"** (secundario, icono copiar). El botón de copiar tiene
  microinteracción: "Copiando…" → "✓ Copiado" 2 s → vuelve al texto original.
  Estado sin agente: mensaje "El agente aún no está disponible. Vuelve pronto."
- **Tarjeta "Plan de grado"** con cabecera azul suave: título, versión y tamaño,
  botón secundario "Descargar plan". Estado sin plan: mensaje equivalente.

### P6 — Panel docente: mis materias

- Título "Mis materias" + subtítulo explicando la responsabilidad (subir el plan).
- Grid de tarjetas de materia con un estado dominante en el pie:
  "● Plan de grado publicado" (verde) o "⚠ Plan de grado pendiente" (ámbar) —
  el pendiente debe llamar la atención.
- Estado vacío: "Aún no tienes materias asignadas…"

### P7 — Panel docente: materia (gestión del plan)

Layout 2/3 + 1/3:
- Izquierda: **tabla de versiones del plan** — título, "v1/v2…", badge Activa/Histórica,
  fecha, acciones (Descargar · Activar · Eliminar). La fila activa lleva fondo verde muy suave.
- Derecha: **tarjeta "Subir nueva versión"**: título prellenado, selector de archivo
  (PDF/DOCX, límite visible), botón primario "Publicar plan". Nota: "la nueva versión
  pasa a ser la activa automáticamente".
- Botón "Ver como estudiante" en el encabezado (enlace a P5).

### P8 — Admin: dashboard

- Sub-navegación admin: segmented control horizontal (Resumen · Facultades · Carreras ·
  Categorías · Materias · Usuarios · Auditoría), pestaña activa en verde pleno. Con scroll
  horizontal en móvil.
- Dos filas de tarjetas-métrica (4 + 4): Facultades, Carreras, Materias, Usuarios
  (clicables) / Agentes activos (verde), Planes publicados (azul), Descargas (dorado),
  **Materias sin agente** — esta última en ámbar cuando es > 0 (es la alerta de trabajo pendiente).

### P9 — Admin: CRUDs simples (Facultades, Categorías)

- Patrón de lista: título + botón primario "Nueva …" a la derecha; tabla con columnas
  esenciales, badges de estado, acciones "Editar / Eliminar" alineadas a la derecha; paginación.
- Patrón de formulario: tarjeta centrada max 640 px, campos apilados con labels arriba,
  notas de ayuda en 12 px, botones "Guardar" (primario) + "Cancelar" (secundario).
- Categorías: selector de color nativo + puntito del color junto al nombre en la tabla.
- Eliminar siempre con confirmación; si tiene dependencias, error explicando por qué no se puede.

### P10 — Admin: carrera con ciclos

- Detalle de carrera: lista de tarjetas por ciclo (encabezado con nombre del ciclo y
  "Eliminar ciclo"), dentro las materias asignadas como filas con enlace.
- Panel lateral: tarjeta "Agregar ciclo" (número 1–12 con el siguiente sugerido, nombre opcional).

### P11 — Admin: materias (la pantalla admin más rica)

- **Lista:** buscador + filtro por categoría; tabla con materia, badge de categoría,
  docente, y columnas "Agente" / "Plan" con Sí (verde) / No (ámbar) para ver huecos de un vistazo.
- **Formulario:** nombre, código, categoría (select), **docente encargado** (select con
  nombre y correo, nota "solo aparecen usuarios con rol docente"), descripción, activa.
- **Detalle** en 2 columnas:
  - "Agente socrático" (cabecera verde): historial de versiones con descargas y autor,
    badge Activo / botón Activar / Descargar / Eliminar; debajo formulario de subida
    (nombre prellenado, descripción, archivo txt/md/json/pdf).
  - "Carreras y ciclos": asignaciones actuales con botón Quitar; formulario de asignación
    con **selects dependientes** (elegir carrera carga sus ciclos).
  - "Planes de grado" (cabecera azul): historial con Descargar/Eliminar (solo lectura del trabajo del docente).

### P12 — Admin: usuarios y roles

- Nota superior: "los usuarios se crean solos en su primer login con Google".
- Buscador por nombre/correo + filtro por rol.
- Tabla: avatar + nombre + correo · badge de rol con color semántico (admin granate,
  docente azul, estudiante verde) · fecha · **cambio de rol inline**: select pequeño +
  botón "Aplicar". En la fila del propio admin: texto gris "No puedes cambiar tu propio rol".

### P13 — Admin: auditoría

- Título + subtítulo "Registro inmutable: quién, qué y cuándo".
- Barra de filtros: usuario, evento (select), desde/hasta (fechas), botón Filtrar.
- Tabla densa: fecha-hora · usuario (nombre + correo) · **badge de evento** con color
  (created verde, updated azul, deleted rojo, eventos personalizados como
  `agent.downloaded` en dorado) · registro afectado ("Faculty #3") · detalle expandible
  ("3 campos" → despliega lista campo: valor) · IP.
- Sin acciones de edición: es solo lectura por diseño.

---

## 4. Reglas transversales de UX

1. **Responsive:** móvil primero para estudiante (P1–P5); las tablas admin colapsan a
   tarjetas o scroll horizontal; la sub-navegación admin hace scroll horizontal.
2. **Estados obligatorios en cada pantalla:** vacío (mensaje amable + siguiente paso),
   error (franja roja con lista), éxito (flash verde con auto-cierre), y confirmación
   nativa antes de toda acción destructiva.
3. **Feedback inmediato:** botones con estado presionado (escala 0.98), hover en tarjetas
   con sombra, microinteracción de "Copiado ✓".
4. **Accesibilidad:** contraste AA, foco visible (anillo azul) en todo elemento interactivo,
   labels en todos los campos, breadcrumbs en toda página interna, `prefers-reduced-motion`.
5. **Jerarquía de acción:** máximo un botón primario verde por vista; lo destructivo
   siempre como enlace/texto rojo, nunca como botón protagonista.
6. **Los indicadores de disponibilidad** (agente/plan) usan siempre el mismo lenguaje
   visual en todo el sistema: ● color = disponible, ○ gris = pendiente.

## 5. Orden de prioridad para diseñar

1. P5 Detalle de materia (define el sistema visual)
2. P2 Catálogo + P4 Carrera (tarjeta de materia)
3. P1 Login
4. P7 Docente gestión de plan
5. P11 Admin materias
6. P8 Dashboard y resto de admin
