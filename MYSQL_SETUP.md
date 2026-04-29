# 🗄️ Configuração do MySQL - Futzin

Sistema Futzin configurado para usar **MySQL** como banco de dados.

---

## 📋 Requisitos

- **MySQL 5.7+** ou **MySQL 8.0+**
- **Credenciais de admin** para criar banco e usuário

---

## 🚀 Setup Rápido

### 1️⃣ Criar Banco de Dados e Usuário

#### Windows (MySQL Command Line)
```bash
mysql -u root -p
```

Dentro do MySQL:
```sql
-- Criar banco de dados
CREATE DATABASE futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário (opcional, usar root é mais simples)
CREATE USER 'futzin'@'localhost' IDENTIFIED BY 'sua_senha_aqui';
GRANT ALL PRIVILEGES ON futzin.* TO 'futzin'@'localhost';
FLUSH PRIVILEGES;

-- Sair
EXIT;
```

#### Mac/Linux
```bash
mysql -u root -p
```

(Mesmo comandos SQL acima)

---

### 2️⃣ Configurar .env

Arquivo já está configurado em **d:\ProjetosWeb\futzin\.env**

Valores atuais:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=futzin
DB_USERNAME=root
DB_PASSWORD=devrobyn
```

**Se você usou outras credenciais, atualize agora:**

```bash
# Windows
notepad .env

# Mac/Linux
nano .env
```

Edite as linhas:
```env
DB_USERNAME=seu_usuario_mysql
DB_PASSWORD=sua_senha_mysql
```

---

### 3️⃣ Rodar Migrations

```bash
# Criar tabelas
php artisan migrate

# Ou com força (ignora warnings)
php artisan migrate --force
```

Deverá exibir:
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table
Migrating: 0001_01_01_000001_create_cache_table
...
✓ 12 migrations rodadas com sucesso
```

---

### 4️⃣ Popular Dados de Teste

```bash
php artisan db:seed
```

Deverá criar:
- 1 admin user
- 10 jogadores
- 1 grupo
- 10 mensalidades
- Rankings

---

### 5️⃣ Verificar Banco

#### Windows
```bash
mysql -u root -p -e "USE futzin; SHOW TABLES;"
```

#### Mac/Linux
```bash
mysql -u root -p -e "USE futzin; SHOW TABLES;"
```

Deverá listar 10 tabelas:
```
users
groups
user_groups
matches
teams
player_match
player_penalties
player_ratings
subscriptions
player_rankings
payouts
```

---

## 🔧 Troubleshooting

### "Access denied for user 'root'"

O MySQL precisa da senha:
```bash
# Adicione a senha
mysql -u root -p -e "SHOW DATABASES;"
# Digite a senha quando pedido
```

### "Can't connect to MySQL server on 'localhost'"

MySQL não está rodando:

**Windows:**
```bash
# Iniciar MySQL
net start MySQL80

# Ou via Services
services.msc
```

**Mac (Homebrew):**
```bash
brew services start mysql
```

**Linux (Ubuntu):**
```bash
sudo systemctl start mysql
```

### "Unknown database 'futzin'"

Banco não foi criado. Execute os comandos SQL de **Setup Rápido - Passo 1**.

### "SQLSTATE[HY000]: General error"

Pode ser erro de character set. Recrie o banco:

```sql
DROP DATABASE futzin;
CREATE DATABASE futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Depois rode migrations novamente.

---

## 📊 Verificar Dados

### Ver Users
```bash
mysql -u root -p futzin -e "SELECT id, name, email FROM users LIMIT 5;"
```

### Ver Grupos
```bash
mysql -u root -p futzin -e "SELECT * FROM groups;"
```

### Ver Rankings
```bash
mysql -u root -p futzin -e "SELECT * FROM player_rankings ORDER BY total_score DESC LIMIT 10;"
```

### Ver Assinaturas
```bash
mysql -u root -p futzin -e "SELECT user_id, plan, status FROM subscriptions;"
```

---

## 🔒 Segurança

### Alterar Senha Root (Recomendado)

```bash
mysql -u root -p
```

Dentro do MySQL:
```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'nova_senha_forte';
FLUSH PRIVILEGES;
EXIT;
```

### Criar Usuário Específico (Melhor Prática)

```sql
CREATE USER 'futzin_user'@'localhost' IDENTIFIED BY 'senha_forte';
GRANT ALL PRIVILEGES ON futzin.* TO 'futzin_user'@'localhost';
FLUSH PRIVILEGES;
```

Depois atualize .env:
```env
DB_USERNAME=futzin_user
DB_PASSWORD=senha_forte
```

---

## 💾 Backup e Restore

### Fazer Backup
```bash
mysqldump -u root -p futzin > backup_futzin.sql
```

### Restaurar
```bash
mysql -u root -p futzin < backup_futzin.sql
```

---

## 🔄 Resetar Banco

```bash
# Dropar todas as tabelas
php artisan migrate:reset

# Ou deletar e recriar do zero
php artisan migrate:refresh --force

# Com seeds
php artisan migrate:refresh --seed --force
```

---

## ✅ Checklist Final

- [ ] MySQL instalado e rodando
- [ ] Banco 'futzin' criado
- [ ] .env configurado com credenciais corretas
- [ ] Migrations rodadas (`php artisan migrate`)
- [ ] Seeders executados (`php artisan db:seed`)
- [ ] Dados aparecem no banco (`mysql -u root -p futzin`)
- [ ] Frontend rodando (`npm run dev`)
- [ ] Login funciona com `admin@futzin.com`

---

## 📞 Próximos Passos

1. Execute: `npm run dev`
2. Abra: http://localhost:5173
3. Faça login: `admin@futzin.com` / `password`
4. Explore o sistema!

---

**MySQL configurado com sucesso! 🎉**
