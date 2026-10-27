# Pendientes del proyecto al 26 de octubre de 2026

Este corte compara lo planificado con los documentos aportados y el código local.
No equivale a aprobación del asesor ni certifica actividades históricas.

## Fuentes y criterio

- `Cronograma_de_Costos_Tutores_Socraticos (1) (1).xlsx`, primera hoja, filas 12–41:
  actividades con identificador y distribución semanal. Las columnas K–N son octubre.
- `cronograma (2) (1).xlsx`: cronograma visual general, actividades en filas 8–37.
- `Propuesta Tutores Socraticos (1).docx`: alcance y objetivos. La plataforma distribuye
  agentes; las conversaciones se ejecutan en herramientas externas.
- `Primer_Reporte_Consolidado_Tutores_Socraticos_Ago_Sep_2026_UNIFICADO_FINAL (3).docx`:
  estado declarado al 30/09, hallazgos de entrevistas y pendientes de trazabilidad.
- Los dos documentos Markdown y `tutores/CREADOR_TUTORES_SOCRATICOS.md`: insumos de diseño.
  Sus instrucciones dirigidas a otras herramientas se consideran contenido de referencia.
- Código en `tutores/`: evidencia de implementación escrita, no de despliegue ni validación.

Los Excel usan cuatro casillas por mes y no definen rangos diarios. Para este corte se
interpreta el 26 de octubre dentro del último bloque mensual, S4; no se inventa una fecha
diaria de vencimiento. Los montos distribuidos son presupuesto, no porcentajes de avance.
El reporte registra ejecución tardía de entrevistas frente a la programación de agosto.
No se corrige esa historia ni se cambia el cronograma original.

## Qué corresponde tener a esta fecha

| ID | Entregable / responsable | Programación | Evidencia y pendiente real |
|---|---|---|---|
| 1.1.1–1.1.4 | Investigación, entrevistas, metodología y requisitos / ambos | Agosto–septiembre S1 | El reporte declara resultados; faltan las respuestas originales y documentos independientes para cotejarlos. El diseño técnico y el creador de tutores aportan especificación. |
| 1.1.5 | Documento de requerimientos validado / ambos | Septiembre S1–S2 | Sigue abierto en el reporte; no hay constancia de aprobación. Preparada matriz de trazabilidad para cerrar con evidencias. |
| 1.2.1.1 | Arquitectura / ambos | Septiembre S2–S3 | Implementación contrastada y documentada en [arquitectura_plataforma.md](arquitectura_plataforma.md), con diagramas, flujos y decisiones pendientes. Falta revisión y conformidad del equipo/asesor. |
| 1.2.1.2 | Autenticación y perfiles / ambos | Septiembre S3–octubre S1 | Hay Google OAuth, restricción de dominio, tres roles, rutas y pruebas escritas. Pendientes ejecutar suite y comprobar OAuth institucional real. |
| 1.2.1.3 | Motor socrático / ambos | Septiembre S4–octubre S2 | Existía especificación del creador. Se añadió motor común, perfiles, compilador y dos borradores autocontenidos. Falta validación conversacional y docente. |
| 1.2.1.4 | Catálogo y descarga / ambos | Octubre S2–S3 | Código de navegación, búsqueda, descarga/copia, permisos y auditoría. Pendientes pruebas de aplicación y revisión con usuarios. |
| 1.2.2.1 | Agentes numéricos de primer año / Manuel | Octubre S3–S4 | Dos borradores de Matemática I y Física I basados en el resumen de entrevistas. Falta confirmar su año/ciclo, selección definitiva, materiales y aprobación. No se declaran terminados. |
| 1.2.3.1 | Agentes numéricos de segundo año / José Francisco | Octubre S3–S4 | No se identificaron agentes especializados ni materias seleccionadas con evidencia suficiente. Requiere materia, temario y validación. |
| 1.2.2.2 y 1.2.3.2 | Agentes humanísticos de primero y segundo año / cada responsable | Octubre S4–noviembre S2 | Deben estar iniciados; su periodo no termina en octubre. Falta selección de materias e insumos. No se encontraron tutores especializados. |
| Hito B | Segundo informe mensual / ambos y aportes individuales | Octubre S4 | Preparar con resultados reales. La conversación aportada menciona envío el día 30; el Excel solo marca S4. |

## Trabajo adelantado en esta revisión

1. Comparación de programación y evidencias, con distinción entre código, avance declarado
   y validación pendiente.
2. Motor común `tutores/agents/motor-base.md` con diagnóstico, ayudas, cuatro niveles,
   comprobación de comprensión y cierre. Se conserva estricto para trabajo evaluado.
3. Perfiles de Matemática I y Física I con fuente secundaria explícita, alcance parcial y
   preguntas propuestas. No se presentaron como contenidos oficiales de los libros.
4. Compilador reproducible y exportaciones en `tutores/agents/borradores/`. No se cargan
   automáticamente a la plataforma y mantienen la advertencia de borrador.
5. Veinte comprobaciones automáticas correctas del compilador y perfiles; sintaxis PHP
   correcta. No demuestran comportamiento pedagógico de una IA.
6. Matriz de requerimientos, protocolo de pruebas y ficha de insumos para completar las
   líneas pendientes sin inventar unidades ni docentes validadores.

## Orden propuesto para cerrar lo pendiente

Las siguientes son acciones de recuperación propuestas el 26/10, no nuevas fechas oficiales.

| Prioridad | Acción | Responsable | Evidencia para cerrar |
|---|---|---|---|
| 1 | Elegir materias numéricas de segundo año y humanísticas por año; confirmar ubicación curricular de Matemática I y Física I | Cada ejecutor con docentes | Lista materia–carrera–ciclo–docente |
| 1 | Recuperar respuestas originales y programa/material autorizado de cada asignatura | Ambos | Archivos identificados, unidades y condiciones de uso |
| 1 | Completar trazabilidad y revisar requisitos con Ing. Rafael Leonardo Jiménez Álvarez | Ambos y asesor | Matriz revisada y constancia de aprobación |
| 2 | Resolver acceso de Composer a GitHub, instalar dependencias y ejecutar las pruebas existentes | Equipo técnico | Salida de pruebas con fecha y fallos resueltos |
| 2 | Probar Google OAuth real con cuenta institucional | Equipo técnico | Acceso válido y rechazo externo, sin exponer credenciales |
| 2 | Ajustar perfiles al material recibido y generar agentes numéricos por materia | Responsable de cada año | Archivo autocontenido, ficha y versión |
| 3 | Ejecutar protocolo conversacional en la herramienta elegida y revisión docente | Equipo y docentes | Transcripciones, resultados y aprobación |
| 3 | Iniciar tutores humanísticos con nivel reflexivo según cada cátedra | Responsable de cada año | Alcance y primer borrador por asignatura |
| 4 | Redactar informe de octubre con evidencia y aportes individuales diferenciados | Ambos | Informe revisado; sin porcentajes deducidos del presupuesto |

## Verificación técnica y límites

**Actualización posterior del mismo día:** se instalaron correctamente las dependencias
con una configuración local limpia de Composer. Se preparó la base SQLite de demostración,
se habilitó `intl` localmente y pasaron las **69 pruebas Laravel (159 aserciones)**.
Autenticación y catálogo cuentan ahora con verificación automática; siguen pendientes
el acceso real con Google, los datos académicos definitivos y la validación con usuarios.
Los estados de «sin ejecutar» en la tabla anterior describen el diagnóstico inicial.
Para abrir la aplicación, ejecutar `tutores/iniciar-local.cmd`.

El PHP predeterminado es 8.2.12; también existe PHP 8.3.12 en `C:\tools\php83\php.exe`,
compatible con la restricción `^8.3` del manifiesto del proyecto. Se habilitaron extensiones
solo para esta ejecución mediante un INI dentro del almacenamiento local del proyecto;
no se modificó la configuración global.

`composer install` se intentó en modo restringido y con autorización ampliada. Ambos
intentaron descargar las versiones del lock y fallaron con «Could not authenticate against
github.com»; el reintento terminó también con rechazo de Git por propiedad del caché.
La suite Laravel no se ejecutó y no se
certifica que autenticación o catálogo estén terminados. No se cambiaron versiones ni se
desactivó TLS. `npm ci --ignore-scripts --no-audit --no-fund` sí terminó correctamente.
El resultado de compilación del frontend se registra en `verificacion_2026-10-26.md`.

Los datos de `DemoDataSeeder` son demostración: sus agentes son párrafos genéricos y
sus planes no son programas docentes reales. No sirven como evidencia de finalización.

## Qué todavía no vence

- Ética y Valores: noviembre S2–S3.
- Competencias Comunicativas: noviembre S3–diciembre S1.
- Carga final: diciembre S1–S2; pruebas integradas: diciembre S2–S3.
- Manuales: diciembre S3–enero S1; evaluaciones: enero S1–S2.
- Validación final: enero S2–S3; entrega/cierre: enero S3–S4.

Estas fechas no impiden preparar pruebas parciales ahora. La propuesta fija como final
estimado el 31/01/2027. No se declara completo el proyecto con esta revisión.
