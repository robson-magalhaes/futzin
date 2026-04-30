# 🔧 Setup - Troubleshooting Atual

**Última Atualização**: 30 de abril de 2026

---

## ✅ Contexto Atual

O projeto usa setup **manual** (sem scripts auxiliares). Isso evita divergências entre Windows, Mac e Linux.

Fluxo oficial:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm install
npm run build
php artisan serve
```

---

## 🐛 Problemas Comuns

### "MySQL not found"

- Windows: instale MySQL e adicione ao PATH
- Mac: `brew install mysql && brew services start mysql`
- Linux: `sudo apt-get install mysql-server && sudo systemctl start mysql`

### "Access denied for user"

Revise as credenciais no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=futzin
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### "Unknown database 'futzin'"

Crie o banco manualmente:

```bash
mysql -u root -p -e "CREATE DATABASE futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --force
php artisan db:seed --force
```

### "php/composer/npm command not found"

- Instale PHP 8.3+
- Instale Composer
- Instale Node.js (npm incluso)
- Reinicie o terminal após instalação

---

## ✅ Verificação Rápida

1. App responde em http://localhost:8000
2. Login com `admin@futzin.com` e `password`
3. Tabelas criadas no banco `futzin`

---

## 🆘 Referências

- [README.md](./README.md)
- [QUICK_START.md](./QUICK_START.md)
- [MYSQL_SETUP.md](./MYSQL_SETUP.md)
