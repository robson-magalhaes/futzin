# ✅ MySQL Configurado - Futzin

**Data**: 25 de abril de 2026  
**Status**: ✅ 100% Configurado e Pronto

---

## 📊 Mudanças Realizadas

### ✅ Arquivos Atualizados
1. **`.env`** - Banco alterado de `laravel` para `futzin`
2. **`.env.example`** - Referência atualizada para MySQL
3. **`setup.bat`** - Adicionado script SQL e verificações MySQL
4. **`setup.sh`** - Adicionado script SQL e verificações MySQL
5. **`README.md`** - Adicionadas instruções MySQL
6. **`QUICK_START.md`** - Adicionado troubleshooting MySQL

### ✅ Arquivos Criados
1. **`MYSQL_SETUP.md`** - Guia completo de configuração MySQL
2. **`create_database.sql`** - Script SQL para criar banco automaticamente

---

## 🔧 Configuração Atual

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=futzin
DB_USERNAME=root
DB_PASSWORD=devrobyn
```

**Alterar em `.env` se suas credenciais são diferentes.**

---

## 🚀 Como Começar (3 passos)

### 1️⃣ Inicie MySQL

**Windows:**
```bash
net start MySQL80
```

**Mac:**
```bash
brew services start mysql
```

**Linux:**
```bash
sudo systemctl start mysql
```

### 2️⃣ Execute Setup

**Windows:**
```bash
.\setup.bat
```

**Mac/Linux:**
```bash
chmod +x setup.sh
./setup.sh
```

### 3️⃣ Inicie Frontend

```bash
npm run dev
```

---

## 📋 O que o Setup Faz

1. ✅ Cria banco de dados `futzin`
2. ✅ Instala PHP dependencies (composer install)
3. ✅ Cria arquivo `.env`
4. ✅ Gera chave da aplicação
5. ✅ Roda 12 migrations
6. ✅ Popula dados de teste (seeder)
7. ✅ Publica configurações Sanctum
8. ✅ Instala npm dependencies
9. ✅ Build frontend assets

---

## 💾 Dados de Teste Criados

```
✓ 1 admin user (admin@futzin.com / password)
✓ 10 jogadores (player1@futzin.com ... player10@futzin.com)
✓ 1 grupo: "Futebol da Terça"
✓ 10 membros no grupo
✓ Assinaturas ativas
✓ 10 mensalidades pendentes
✓ Rankings com notas aleatórias
```

---

## 🔐 Credenciais

### Admin (Teste)
```
Email: admin@futzin.com
Senha: password
Plano: Premium (ativo)
```

### MySQL (Editar em .env)
```
Host: 127.0.0.1
Port: 3306
Database: futzin
Username: root (padrão)
Password: devrobyn (editar se necessário)
```

---

## 🐛 Troubleshooting MySQL

### "Can't connect to MySQL server"
MySQL não está rodando. Veja instruções em [MYSQL_SETUP.md](./MYSQL_SETUP.md).

### "Access denied for user 'root'"
Verifique senha em `.env`:
```env
DB_PASSWORD=sua_senha_aqui
```

### "Unknown database 'futzin'"
Execute manualmente:
```bash
mysql -u root -p < create_database.sql
```

Para mais detalhes, consulte [MYSQL_SETUP.md](./MYSQL_SETUP.md).

---

## 📚 Documentação

- **[QUICK_START.md](./QUICK_START.md)** - Guia rápido (comece aqui!)
- **[MYSQL_SETUP.md](./MYSQL_SETUP.md)** - Guia completo MySQL
- **[README.md](./README.md)** - Documentação completa
- **[ARCHITECTURE.md](./ARCHITECTURE.md)** - Arquitetura técnica
- **[VALIDATION_CHECKLIST.md](./VALIDATION_CHECKLIST.md)** - Testes e validação

---

## ✅ Próximos Passos

1. **Instale MySQL** (se não tiver)
2. **Execute `.\setup.bat`** (Windows) ou `./setup.sh` (Mac/Linux)
3. **Execute `npm run dev`**
4. **Acesse http://localhost:5173**
5. **Faça login com `admin@futzin.com` / `password`**

---

## 📊 Verificar Status

### Verificar se MySQL está rodando
```bash
mysql -u root -p -e "SELECT 1;"
```

### Verificar banco de dados
```bash
mysql -u root -p -e "USE futzin; SHOW TABLES;"
```

### Ver dados
```bash
mysql -u root -p futzin -e "SELECT * FROM users;"
```

---

**MySQL está 100% configurado e pronto! 🎉**

Próximo: Execute o setup script e comece a usar!
