#!/bin/bash

# Sistema Futzin - Setup Script for Mac/Linux
set -e

echo "=========================================="
echo "FUTZIN - Setup Script for Mac/Linux"
echo "=========================================="
echo ""

# Criar arquivo .env
echo "[*] Configurando ambiente..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "[OK] .env criado"
else
    echo "[OK] .env ja existe"
fi

# Gerar chave da aplicação
php artisan key:generate --force
echo "[OK] Chave da aplicacao gerada"
echo ""

# Criar banco de dados MySQL
echo "[*] Criando banco de dados MySQL..."
mysql -u root -pdevrobyn < create_database.sql 2>/dev/null || mysql -u root < create_database.sql
echo "[OK] Banco de dados criado"
echo ""

# Instalar dependências PHP
echo "[*] Instalando dependencias PHP..."
composer install --no-interaction --optimize-autoloader
echo "[OK] PHP dependencies instaladas"
echo ""

# Publicar Sanctum (antes de migrations)
echo "[*] Publicando Sanctum..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
echo "[OK] Sanctum publicado"
echo ""

# Rodar migrações
echo "[*] Executando migracoes..."
php artisan migrate --force
echo "[OK] Migracoes executadas"
echo ""

# Semear dados de teste
echo "[*] Populando banco com dados de teste..."
php artisan db:seed --force
echo "[OK] Dados de teste criados"
echo ""

# Instalar dependências Node
echo "[*] Instalando dependencias Node..."
npm install
echo "[OK] Node dependencies instaladas"
echo ""

# Buildar assets
echo "[*] Buildando assets frontend..."
npm run build
echo "[OK] Assets buildados"
echo ""

echo "=========================================="
echo "[OK] Setup concluido com sucesso!"
echo "=========================================="
echo ""
echo "Proximos passos:"
echo "  1. Terminal 1: php artisan serve"
echo "  2. Terminal 2: npm run dev"
echo ""
echo "Depois acesse:"
echo "  Frontend: http://localhost:5173"
echo "  Email: admin@futzin.com"
echo "  Senha: password"
echo ""
