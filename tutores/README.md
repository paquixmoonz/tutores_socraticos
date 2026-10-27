# Sistema de Tutores Socráticos — UNICAES

Catálogo institucional de agentes socráticos basados en IA. Los estudiantes navegan
**Facultad → Carrera → Ciclo → Materia**, consultan el plan de grado y **descargan el agente**
para usarlo en su herramienta de IA preferida (ChatGPT, Claude, Gemini…). La plataforma no
ejecuta los agentes: los distribuye con control de acceso, roles y auditoría.

**Stack:** Laravel 13 (monolito) · Blade + Tailwind CSS 4 · SQLite/MySQL ·
Google OAuth (Socialite) · spatie/laravel-permission · owen-it/laravel-auditing.

La [arquitectura de la plataforma](../docs/arquitectura_plataforma.md) documenta los
componentes, modelo del catálogo, permisos, flujos y decisiones pendientes de revisión.

## Requisitos

- PHP 8.3+ con extensiones: `curl`, `pdo_sqlite` (o `pdo_mysql`), `mbstring`, `openssl`, `fileinfo`, `zip`, `intl`
- Composer 2
- Node.js 18+

## Puesta en marcha (desarrollo)

```bash
cd tutores
composer install
npm install

cp .env.example .env        # si no existe .env
php artisan key:generate

php artisan migrate --seed  # crea BD SQLite, roles, facultades, categorías y datos demo
npm run build               # o `npm run dev` durante el desarrollo

php artisan serve           # http://localhost:8000
```

En entorno **local** la pantalla de login muestra un panel de **“Acceso de desarrollo”**
para entrar como admin/docente/estudiante demo sin credenciales de Google.
Ese panel y sus rutas **no existen** fuera de `APP_ENV=local`.

### Iniciar en este equipo Windows

Con el entorno local ya preparado, desde la carpeta `tutores` ejecuta:

```powershell
.\iniciar-local.cmd
```

Abre **http://127.0.0.1:8000/login** y despliega **Acceso de desarrollo (solo local)**.
Puedes elegir Administrador UNICAES, Docente de Demostración o Estudiante de Demostración.
Este acceso permite revisar las pantallas sin configurar Google. Los datos y documentos
incluidos son demostraciones. Para detener el servidor, presiona Ctrl+C en la terminal.

El lanzador utiliza PHP en `C:\tools\php83` cuando está disponible y carga las extensiones
configuradas para este equipo en el almacenamiento local, sin modificar el PHP global.

## Configurar Google OAuth (login institucional real)

1. Entra a [Google Cloud Console](https://console.cloud.google.com/) → crea un proyecto.
2. **APIs y servicios → Pantalla de consentimiento OAuth**: tipo *Interno* (si el dominio
   `catolica.edu.sv` usa Google Workspace) o *Externo*.
3. **Credenciales → Crear credenciales → ID de cliente de OAuth** → tipo *Aplicación web*:
   - Orígenes autorizados: `http://localhost:8000` (y el dominio de producción)
   - URI de redirección: `http://localhost:8000/auth/google/callback`
4. Copia el Client ID y el Secret al `.env`:

```env
GOOGLE_CLIENT_ID=xxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

> Solo se aceptan cuentas `@catolica.edu.sv` (configurable con `TUTORES_ALLOWED_DOMAIN`).
> El primer usuario admin se define con `TUTORES_ADMIN_EMAIL` y se crea al ejecutar los seeders.

## Roles

| Rol | Capacidades |
|-----|-------------|
| estudiante | Navega el catálogo, ve planes, descarga/copia agentes (rol por defecto) |
| docente | Lo anterior + sube y versiona planes de grado de **sus** materias |
| admin | Control total: catálogo, roles, agentes, auditoría |

## Tests

```bash
php artisan test    # auth, roles, documentos, CRUDs, catálogo
```

## Producción — lista de verificación

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` con HTTPS
- Base de datos MySQL 8 (`DB_CONNECTION=mysql` + credenciales)
- Credenciales reales de Google OAuth con la URI de producción
- `TUTORES_ADMIN_EMAIL` con el correo real del administrador
- `php artisan migrate --force` y seeders base (los datos demo solo corren en local)
- `npm run build` y `php artisan config:cache route:cache view:cache`
