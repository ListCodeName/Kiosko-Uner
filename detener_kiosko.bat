@echo off
cd /d "%~dp0"
echo [INFO] Buscando proceso activo de php artisan serve en el puerto 8000...

:: Verificar si el puerto 8000 esta en uso
netstat -o -an | findstr /i "listening" | findstr ":8000" >nul

if "%errorlevel%" equ "0" (
    echo [INFO] Servidor detectado. Cerrando procesos...
    for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000 ^| findstr LISTENING') do taskkill /f /pid %%a
    echo [OK] El servidor del Kiosko se ha detenido correctamente.
) else (
    echo [INFO] El servidor del Kiosko no estaba activo.
)

timeout /t 2 >nul
