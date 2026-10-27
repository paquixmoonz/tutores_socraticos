# Motor y borradores socráticos

El motor combina reglas comunes con un perfil por asignatura para producir un archivo
autocontenido que el estudiante puede copiar a una herramienta de IA. El compilador no
ejecuta conversaciones ni requiere Laravel, credenciales o una API de IA.

Desde `tutores`, ejecutar:

```powershell
php agents/verificar.php
php agents/compilar.php
```

El resultado queda en `agents/borradores/`. Cada ejecución reemplaza las exportaciones
de los perfiles presentes; editar los JSON y el motor, no los archivos generados. Los
archivos de perfiles retirados deben revisarse manualmente en la carpeta de borradores.
El compilador siempre marca la salida como borrador: no registra aprobación docente.

Matemática I y Física I se basan en los hallazgos resumidos del informe consolidado.
Sus preguntas guía y mini-evaluaciones son propuestas nuevas por validar. No incorporan
el texto de los libros recomendados ni un programa oficial completo. Falta confirmar el
año/ciclo, especialmente antes de imputarlas a una línea del cronograma.

Para preparar otro tutor, copiar un perfil y completar sus campos con el programa y la
entrevista. Niveles admitidos: `estricto`, `progresivo`, `reflexivo`, `redaccion`.
Ver `../../docs/insumos_por_asignatura.md` y `../../docs/validacion_tutores.md`.

Para una revisión conversacional, copiar todo el borrador a un chat nuevo de la herramienta
elegida y ejecutar los casos del protocolo. No hay instrucciones dependientes de menús,
planes comerciales o prestaciones particulares de un servicio.

Ficha técnica común de esta entrega: motor v0.1, perfiles v0.1-borrador, fecha 26/10/2026,
validación docente pendiente, resultados conversacionales pendientes, fuente secundaria
identificada dentro de cada archivo. La ficha específica incluye materia, área, rigor,
alcance, temas y vacíos. Las fuentes recomendadas se enumeran en el documento de insumos.

Cuando estén revisados y aprobados, el administrador puede subir el archivo de la materia
mediante el módulo de agentes existente. No se cargan estos borradores automáticamente
ni se reemplazan datos de demostración. La integración final está programada para diciembre.
