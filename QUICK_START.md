# 🚀 Quick Start Guide - Futzin

## ⚠️ Pré-requisitos

- **MySQL** instalado e rodando
  - Windows: https://dev.mysql.com/downloads/mysql/
  - Mac: `brew install mysql && brew services start mysql`
  - Linux: `sudo apt-get install mysql-server && sudo systemctl start mysql`

---

## 1️⃣ Clone e Setup (2-5 minutos)

### Windows / Mac / Linux
```bash
cd d:\ProjetosWeb\futzin
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm install
npm run build
```

---

## 2️⃣ Inicie o Desenvolvimento

```bash
php artisan serve
```

## 3️⃣ Primeira Sessão

Abra http://localhost:8000 no navegador

**Login com:**
- Email: `admin@futzin.com`
- Senha: `password`

## 4️⃣ Explore

### Dashboard
Visualize sua assinatura Premium e grupos

### Grupos
- Você já está no grupo "Futebol da Terça"
- Veja os 10 membros
- Clique em "Ver Ranking" para ver os rankings

### Ranking
Tabela com:
- Posição
- Nome do jogador
- Partidas jogadas
- Gols, assistências, MVPs
- Nota média
- Pontuação total

### Assinatura
- Veja seu plano Premium
- Opções para cancelar (não cancele!)

### Pagamentos
- Veja as mensalidades pendentes

---

## 🔄 Ciclo de Desenvolvimento

### Fazer Alterações

1. **Backend** (PHP/Laravel)
   - Edite em `app/`
   - As migrações já foram rodadas
   - Se criar nova migration: `php artisan migrate`

2. **Frontend** (Blade + Tailwind)
  - Edite em `resources/views/`
  - Compile assets com `npm run build` quando necessário

### Rodar Comandos Úteis

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear

# Resetar DB
php artisan migrate:refresh --force
php artisan db:seed

# Logs em tempo real
php artisan tail

# Logs avançados (Linux/macOS com pcntl)
composer dev:with-logs

# Build para produção
npm run build
```

---

## 🧪 Testar a API Diretamente

Use **Postman** ou **Insomnia**:

### 1. Login
```http
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "admin@futzin.com",
  "password": "password"
}
```

Você receberá um `token` de autenticação.

### 2. Usar em Requisições
```http
GET http://localhost:8000/api/groups
Authorization: Bearer {seu_token}
```

### 3. Listar Rankings
```http
GET http://localhost:8000/api/rankings/group/1
Authorization: Bearer {seu_token}
```

---

## 📱 Testar no Mobile

Sua aplicação está em `http://localhost:8000`

Para acessar do celular:
1. Descubra seu IP local: `ipconfig` (Windows) ou `ifconfig` (Mac/Linux)
2. No celular acesse: `http://SEU_IP:8000`

---

## 🐛 Problemas MySQL

### "Can't connect to MySQL server"

MySQL não está rodando:

**Windows:**
```bash
# Iniciar MySQL via Services
net start MySQL80

# Ou abrir Services (services.msc) e iniciar "MySQL80"
```

**Mac:**
```bash
brew services start mysql
```

**Linux:**
```bash
sudo systemctl start mysql
```

### "Access denied for user 'root'"

Verifique credenciais em `.env`:
```env
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

Se não sabe a senha:
```bash
# Resetar senha (Mac/Linux)
mysql -u root -e "FLUSH PRIVILEGES;"

# Windows: usar MySQL Workbench GUI
```

### "Unknown database 'futzin'"

Crie o banco manualmente e rode as migrations:
```bash
mysql -u root -p -e "CREATE DATABASE futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --force
```

### "Collation mismatch"

Recrie o banco com charset correto:
```bash
DROP DATABASE futzin;
CREATE DATABASE futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
php artisan migrate --force
php artisan db:seed
```

---

## 🐛 Problemas Comuns

### "Porta 8000 já está em uso"
```bash
# Use outra porta
php artisan serve --port=8001
```

### "npm: command not found"
Instale Node.js de https://nodejs.org

### "composer: command not found"
Instale Composer de https://getcomposer.org

### "Erro de CORS"
No fluxo Blade padrão, app e backend rodam no mesmo host/porta.  
Se você tiver integração externa, revise `APP_URL` no `.env`.

### "Migrations failed"
```bash
# Reset completo
php artisan migrate:refresh --force
php artisan db:seed
```

---

## 📖 Documentação Completa

Veja [README.md](./README.md) e [DELIVERY_SUMMARY.md](./DELIVERY_SUMMARY.md)

---

## 🎯 Próximas Tarefas

- [ ] Integrar pagamento real (Stripe)
- [ ] Adicionar testes automatizados
- [ ] Implementar MVP voting
- [ ] Criar admin dashboard
- [ ] Notificações em tempo real
- [ ] Mobile app nativa (React Native)

---

**Pronto? Vamos jogar! ⚽**
