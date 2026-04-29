@echo off
REM Iniciar Futzin - Execute em 2 terminais diferentes

if "%1%"=="server" (
    echo Iniciando servidor Laravel...
    php artisan serve
    pause
) else if "%1%"=="dev" (
    echo Iniciando dev server frontend...
    npm run dev
    pause
) else (
    echo.
    echo ==========================================
    echo        FUTZIN - Inicializacao Rapida
    echo ==========================================
    echo.
    echo Execute em 2 terminais diferentes:
    echo.
    echo   Terminal 1:
    echo   .\start.bat server
    echo.
    echo   Terminal 2:
    echo   .\start.bat dev
    echo.
    echo Depois acesse:
    echo   http://localhost:5173
    echo   Email: admin@futzin.com
    echo   Senha: password
    echo.
    pause
)
