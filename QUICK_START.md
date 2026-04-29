# 🚀 Quick Start Guide - Futzin

## ⚠️ Pré-requisitos

- **MySQL** instalado e rodando
  - Windows: https://dev.mysql.com/downloads/mysql/
  - Mac: `brew install mysql && brew services start mysql`
  - Linux: `sudo apt-get install mysql-server && sudo systemctl start mysql`

---

## 1️⃣ Clone e Setup (2-5 minutos)

### Windows
```batch
cd d:\ProjetosWeb\futzin
.\setup.bat
```

### Mac/Linux
```bash
cd ~/projetos/futzin
chmod +x setup.sh
./setup.sh
```

**O script vai:**
- ✓ Verificar se MySQL está rodando
- ✓ Criar banco de dados `futzin`
- ✓ Instalar dependências (PHP e npm)
- ✓ Rodar migrations
- ✓ Popular dados de teste
- ✓ Build frontend assets

---

## 2️⃣ Inicie o Desenvolvimento

```bash
composer dev
```

Será exibido:
```
➜  Local:   http://localhost:5173/
➜  press h to show help
```

## 3️⃣ Primeira Sessão

Abra http://localhost:5173 no navegador

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

2. **Frontend** (Vue)
   - Edite em `resources/js/`
   - Hot reload automático com Vite
   - Tailwind com JIT compilation

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

Seu frontend está em `http://localhost:5173`

Para acessar do celular:
1. Descubra seu IP local: `ipconfig` (Windows) ou `ifconfig` (Mac/Linux)
2. No celular acesse: `http://SEU_IP:5173`

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

Execute manualmente:
```bash
mysql -u root -p < create_database.sql
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
O backend responde em porta 8000, frontend em 5173  
Se mudar portas, atualize URLs em `resources/js/services/api.js`

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
