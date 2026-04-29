@echo off
REM Sistema Futzin - Setup Script for Windows
setlocal enabledelayedexpansion

color 0A
title FUTZIN Setup

echo ==========================================
echo FUTZIN - Setup Script for Windows
echo ==========================================
echo.

REM Resolver comando do MySQL (PATH, variavel de ambiente ou caminhos comuns)
set "MYSQL_CMD=mysql"
where mysql >nul 2>&1
if errorlevel 1 (
    if defined MYSQL_EXE if exist "%MYSQL_EXE%" set "MYSQL_CMD=%MYSQL_EXE%"
    if "!MYSQL_CMD!"=="mysql" if defined MYSQL_BIN if exist "%MYSQL_BIN%\mysql.exe" set "MYSQL_CMD=%MYSQL_BIN%\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe" set "MYSQL_CMD=C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\Program Files\MySQL\MySQL Server 8.3\bin\mysql.exe" set "MYSQL_CMD=C:\Program Files\MySQL\MySQL Server 8.3\bin\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\Program Files\MySQL\MySQL Server 8.2\bin\mysql.exe" set "MYSQL_CMD=C:\Program Files\MySQL\MySQL Server 8.2\bin\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\Program Files\MySQL\MySQL Server 8.1\bin\mysql.exe" set "MYSQL_CMD=C:\Program Files\MySQL\MySQL Server 8.1\bin\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" set "MYSQL_CMD=C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\Program Files\MySQL\MySQL Server 5.7\bin\mysql.exe" set "MYSQL_CMD=C:\Program Files\MySQL\MySQL Server 5.7\bin\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\xampp\mysql\bin\mysql.exe" set "MYSQL_CMD=C:\xampp\mysql\bin\mysql.exe"
    if "!MYSQL_CMD!"=="mysql" if exist "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" set "MYSQL_CMD=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe"
)

if "!MYSQL_CMD!"=="mysql" (
    where mysql >nul 2>&1
    if errorlevel 1 (
        echo [!] Nao foi possivel localizar o mysql.exe
        echo     Defina MYSQL_EXE com caminho completo do executavel, por exemplo:
        echo     set MYSQL_EXE=C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe
        echo     ou adicione o diretorio bin do MySQL no PATH.
        pause
        exit /b 1
    )
)

REM Criar arquivo .env
echo [*] Configurando ambiente...
if not exist .env (
    copy .env.example .env
    echo [OK] .env criado
) else (
    echo [OK] .env ja existe
)
echo.

REM Gerar chave da aplicacao
echo [*] Gerando chave da aplicacao...
call php artisan key:generate --force
if errorlevel 1 (
    echo [!] Erro ao gerar chave!
    pause
    exit /b 1
)
echo [OK] Chave gerada
echo.

REM Criar banco de dados MySQL
echo [*] Criando banco de dados MySQL...
REM Tenta sem senha
"%MYSQL_CMD%" -u root -e "SELECT 1" >nul 2>&1
if !errorlevel! equ 0 (
    echo [OK] MySQL conectado sem senha
    "%MYSQL_CMD%" -u root < create_database.sql
) else (
    REM Tenta com senha
    "%MYSQL_CMD%" -u root -pdevrobyn -e "SELECT 1" >nul 2>&1
    if !errorlevel! equ 0 (
        echo [OK] MySQL conectado com senha 'devrobyn'
        "%MYSQL_CMD%" -u root -pdevrobyn < create_database.sql
    ) else (
        echo [!] Nao conseguiu conectar ao MySQL!
        echo     Verifique as credenciais em .env
        echo     DB_USERNAME e DB_PASSWORD
        pause
        exit /b 1
    )
)
if errorlevel 1 (
    echo [!] Erro ao criar banco de dados!
    pause
    exit /b 1
)
echo [OK] Banco de dados criado
echo.

REM Instalar dependencias PHP
echo [*] Instalando dependencias PHP...
call composer install --no-interaction --optimize-autoloader
if errorlevel 1 (
    echo [!] composer install falhou. Tentando corrigir lock com composer update...
    call composer update --no-interaction --optimize-autoloader
    if errorlevel 1 (
        echo [!] Erro ao atualizar dependencias PHP!
        pause
        exit /b 1
    )
)
echo [OK] PHP dependencies instaladas
echo.

REM Publicar Sanctum
echo [*] Publicando Sanctum...
dir /b database\migrations\*_create_personal_access_tokens_table.php >nul 2>&1
if errorlevel 1 (
    call php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
    if errorlevel 1 (
        echo [!] Erro ao publicar Sanctum!
        pause
        exit /b 1
    )
    echo [OK] Sanctum publicado
) else (
    echo [OK] Sanctum ja publicado
)
echo.
echo [*] Executando migracoes...
call php artisan migrate --force
if errorlevel 1 (
    echo [!] Erro nas migracoes!
    pause
    exit /b 1
)
echo [OK] Migracoes executadas
echo.

REM Semear dados de teste
echo [*] Populando banco com dados de teste...
call php artisan db:seed --force
if errorlevel 1 (
    echo [!] Erro ao popular banco!
    pause
    exit /b 1
)
echo [OK] Dados de teste criados
echo.

REM Instalar dependencias Node
echo [*] Instalando dependencias Node...
call npm install
if errorlevel 1 (
    echo [!] Erro ao instalar npm!
    pause
    exit /b 1
)
echo [OK] Node dependencies instaladas
echo.

REM Buildar assets
echo [*] Buildando assets frontend...
call npm run build
if errorlevel 1 (
    echo [!] Erro ao buildar assets!
    pause
    exit /b 1
)
echo [OK] Assets buildados
echo.

echo ==========================================
echo [OK] Setup concluido com sucesso!
echo ==========================================
echo.
echo Proximos passos:
echo   Terminal 1: php artisan serve
echo   Terminal 2: npm run dev
echo.
echo Depois acesse:
echo   Frontend: http://localhost:5173
echo   Email: admin@futzin.com
echo   Senha: password
echo.
pause
