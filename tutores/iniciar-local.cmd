@echo off
setlocal
cd /d "%~dp0"
if not exist "vendor\autoload.php" (
    echo Primero se deben instalar las dependencias. Ver README.md.
    exit /b 1
)
if not exist ".env" (
    echo Primero se debe configurar el entorno local. Ver README.md.
    exit /b 1
)
set "TUTORES_PHP=php"
if exist "C:\tools\php83\php.exe" (
    set "TUTORES_PHP=C:\tools\php83\php.exe"
    set "PATH=C:\tools\php83;%PATH%"
)
if exist "%~dp0storage\framework\testing\php83\extensions.ini" set "PHP_INI_SCAN_DIR=%~dp0storage\framework\testing\php83"
echo Abri http://127.0.0.1:8000/login y elegi Acceso de desarrollo ^(solo local^).
echo Para detener el servidor, presiona Ctrl+C.
"%TUTORES_PHP%" artisan serve --host=127.0.0.1 --port=8000
exit /b %ERRORLEVEL%
