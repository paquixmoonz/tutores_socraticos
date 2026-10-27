# Evidencia de verificación — 26/10/2026

## Actualización: ejecución local posterior

Se resolvió la descarga de Composer usando una configuración y caché locales limpios,
sin cambiar credenciales globales. Las 119 dependencias del lock se instalaron correctamente.
Se preparó `.env` local, clave de aplicación y base SQLite con migraciones y datos demo.
Se habilitó `intl` en el INI local después de que una prueba detectara su ausencia.

La suite terminó correctamente: **69 pruebas, 159 aserciones**. Incluye autenticación
simulada, permisos, catálogo y documentos; no sustituye una prueba real con Google.
El lanzador `tutores/iniciar-local.cmd` permite abrir el servidor en 127.0.0.1:8000.
Los fallos de instalación descritos debajo son antecedentes de la revisión inicial.

| Comprobación ejecutada | Resultado | Qué permite concluir |
|---|---|---|
| `php agents/verificar.php` | 20 comprobaciones correctas | Los perfiles conservan fuente, advertencia, evaluación y pendientes. Se validan cuatro niveles y se rechazan seis entradas inválidas. |
| `php agents/compilar.php` | Generó `fisica-i.md` y `matematica-i.md` | Ambos borradores se ensamblan con el mismo motor y su ficha específica. |
| `php -l agents/compilar.php` y `php -l agents/verificar.php` | Sin errores de sintaxis | Los scripts nuevos pueden analizarse con el PHP disponible. |
| `npm ci --ignore-scripts --no-audit --no-fund` | Correcto: 60 paquetes instalados | Dependencias del frontend disponibles desde el lock. No se ejecutó auditoría de dependencias. |
| `npm run build` | Correcto: Vite generó manifiesto, CSS, JS y fuentes | El frontend compila. No demuestra funcionamiento de las rutas Laravel ni revisión visual en navegador. |
| `composer install --no-interaction --prefer-dist` con PHP 8.3.12 | Falló en descarga de dependencias | No se pudo ejecutar la suite Laravel. |
| Reintento autorizado de Composer | Persistieron errores de autenticación GitHub; la salida final incluyó rechazo de Git por propiedad dudosa en un repositorio del caché | No se modificó la configuración global de Git ni se solicitaron credenciales. Requiere resolver acceso/entorno antes de probar la aplicación. |

La compilación emitió una advertencia no bloqueante: los ajustes opcionales de fuentes
requieren `fontaine` o desactivar `optimizedFallbacks`. No se añadió una dependencia por ello.

No se ejecutaron conversaciones con IA, entrevistas, validación docente, acceso Google
real, pruebas de usuario, publicación web ni carga de agentes a una base de datos.
Las pruebas existentes se inspeccionaron como código, sin atribuirles resultados aprobados.

Las dependencias, cachés, INI temporal y archivos compilados permanecen en ubicaciones
locales ignoradas por Git. No se editaron el cronograma ni los documentos originales.
