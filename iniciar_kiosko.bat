@echo off
:: Navega a la carpeta del proyecto de manera segura
cd /d "%~dp0"

:: Verificar si el puerto 8000 ya esta siendo utilizado
netstat -o -an | findstr /i "listening" | findstr ":8000" >nul

if "%errorlevel%" equ "0" (
    echo [INFO] El servidor ya esta levantado. Abriendo el Kiosko en el navegador...
    start http://127.0.0.1:8000
) else (
    echo [INFO] Iniciando el servidor del Kiosko de Laravel...
    :: Inicia php artisan serve de forma completamente invisible en segundo plano usando PowerShell
    powershell -WindowStyle Hidden -Command "Start-Process php -ArgumentList 'artisan serve' -WindowStyle Hidden"
    
    :: Esperar 2 segundos para dar tiempo al servidor a iniciarse
    timeout /t 2 /nobreak >nul
    
    echo [INFO] Abriendo el Kiosko en el navegador...
    start http://127.0.0.1:8000
)
