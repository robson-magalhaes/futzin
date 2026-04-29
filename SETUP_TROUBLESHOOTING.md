# 🔧 Setup - Troubleshooting e Correções

**Última Atualização**: 25 de abril de 2026

---

## ✅ Problemas Corrigidos

### 1. **Emojis Causando Erro**
Os emojis nos scripts podem causar problemas de encoding em alguns shells/sistemas.

**Solução**: ✓ Removidos todos os emojis dos scripts

### 2. **Comando MySQL Interativo Travando**
O comando `mysql -u root -p < create_database.sql` trava porque tenta ler senha interativamente E ler do pipe simultaneamente.

**Solução**: ✓ Agora tenta sem senha primeiro, depois com senha em formato não-interativo

### 3. **Falta de Verificação de Erros**
Se um comando falhasse, o script continuava executando comandos subsequentes.

**Solução**: ✓ Adicionadas verificações de erro após cada comando crítico

### 4. **Problemas com Character Set**
Em alguns sistemas, caracteres especiais não eram bem interpretados.

**Solução**: ✓ Scripts simplificados com caracteres ASCII apenas

---

## 🚀 Como Executar (Versão Corrigida)

### Windows

```bash
# Certifique-se que está na pasta do projeto
cd d:\ProjetosWeb\futzin

# Execute o script
.\setup.bat
```

**O script vai:**
1. Verificar se MySQL está instalado
2. Criar/confirmar arquivo `.env`
3. Gerar chave da aplicação
4. Criar banco `futzin` no MySQL
5. Instalar dependências PHP (Composer)
6. Rodar migrations
7. Popular dados de teste
8. Publicar Sanctum
9. Instalar dependências npm
10. Buildar assets
11. Exibir próximos passos

### Mac/Linux

```bash
# Certifique-se que está na pasta do projeto
cd ~/projetos/futzin

# Dê permissão de execução
chmod +x setup.sh

# Execute o script
./setup.sh
```

---

## 🐛 Se Ainda Tiver Erros

### Erro 1: "MySQL not found"
```
[!] MySQL nao encontrado!
```

**Solução**:
- **Windows**: Instale MySQL de https://dev.mysql.com/downloads/mysql/
- **Mac**: `brew install mysql && brew services start mysql`
- **Linux**: `sudo apt-get install mysql-server && sudo systemctl start mysql`

### Erro 2: "Access denied for user 'root'"
```
[!] Nao conseguiu conectar ao MySQL!
```

**Verifique .env**:
```bash
cat .env | grep DB_
```

Deve mostrar:
```
DB_USERNAME=root
DB_PASSWORD=devrobyn
```

Se suas credenciais são diferentes, atualize `.env` e rode novamente.

### Erro 3: "Unknown database 'futzin'"
```
[!] Erro ao criar banco de dados!
```

**Solução Manual**:
```bash
mysql -u root -p
# Digite sua senha do MySQL

# Dentro do MySQL:
CREATE DATABASE futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Depois rode as migrações:
php artisan migrate --force
php artisan db:seed
```

### Erro 4: "php: command not found"
```
[!] Php nao encontrado!
```

**Solução**:
- Instale PHP 8.3+ de https://www.php.net/downloads
- Adicione PHP ao PATH do seu sistema

### Erro 5: "composer: command not found"
```
[!] Composer nao encontrado!
```

**Solução**:
- Instale Composer de https://getcomposer.org/download/
- Adicione ao PATH do seu sistema

### Erro 6: "npm: command not found"
```
[!] npm nao encontrado!
```

**Solução**:
- Instale Node.js de https://nodejs.org/
- npm vem incluído

---

## 🔄 Executar Setup Manualmente (Se Tiver Muitos Problemas)

Se o script continuar dando erros, execute passo a passo:

```bash
# 1. Criar banco de dados MySQL
mysql -u root -p
# ou
mysql -u root -pSUA_SENHA

# Dentro do MySQL:
CREATE DATABASE futzin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 2. Configurar .env
cp .env.example .env
php artisan key:generate

# 3. Instalar dependências PHP
composer install

# 4. Rodar migrações
php artisan migrate --force

# 5. Popular dados de teste
php artisan db:seed

# 6. Publicar Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force

# 7. Instalar dependências npm
npm install

# 8. Buildar assets
npm run build

# 9. Inicie o dev server
npm run dev
```

---

## ✅ Verificar se Funcionou

### 1. Banco de dados criado
```bash
mysql -u root -p
# ou
mysql -u root -pSUA_SENHA

# Dentro do MySQL:
SHOW DATABASES;
# Deve incluir "futzin"

USE futzin;
SHOW TABLES;
# Deve mostrar 11 tabelas

SELECT * FROM users;
# Deve mostrar 11 usuários (1 admin + 10 players)
```

### 2. Frontend rodando
```bash
npm run dev
```

Abra: http://localhost:5173

### 3. Login funciona
- Email: `admin@futzin.com`
- Senha: `password`

---

## 📝 Mudanças nos Scripts

### setup.sh
- ✓ Removidos emojis
- ✓ Adicionado `set -e` para parar em erros
- ✓ Verifica se MySQL está instalado
- ✓ Tenta conectar MySQL sem senha, depois com
- ✓ Melhor feedback de status

### setup.bat
- ✓ Removidos emojis
- ✓ Adicionadas verificações de erro com `errorlevel`
- ✓ Usa `where` para verificar MySQL no PATH
- ✓ Tenta conectar MySQL sem senha, depois com
- ✓ Melhor feedback visual

---

## 💾 Criar Script Personalizado

Se quiser usar credenciais diferentes do MySQL, crie um arquivo `setup-custom.sh`:

```bash
#!/bin/bash

# Setup customizado com suas credenciais
DB_USER="seu_usuario"
DB_PASSWORD="sua_senha"
DB_NAME="futzin"

echo "Criando banco com usuario: $DB_USER"

# Criar banco
mysql -u $DB_USER -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Resto do setup...
php artisan migrate --force
php artisan db:seed
composer install
npm install
npm run build
```

---

## 🆘 Suporte

Se ainda tiver problemas:

1. **Leia** [MYSQL_SETUP.md](./MYSQL_SETUP.md) - Setup MySQL detalhado
2. **Veja** [QUICK_START.md](./QUICK_START.md) - Troubleshooting geral
3. **Execute manualmente** os passos em "Executar Setup Manualmente"
4. **Verifique** se MySQL está rodando: `mysql -u root -p`

---

## ✨ Scripts Atualizados

- ✅ [setup.bat](./setup.bat) - Sem emojis, com verificações
- ✅ [setup.sh](./setup.sh) - Sem emojis, com verificações
- ✅ [create_database.sql](./create_database.sql) - SQL puro, sem emojis

**Tudo pronto para executar! 🚀**
