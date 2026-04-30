# 🚀 Executar Futzin - Guia Rápido

MySQL já está instalado! Vamos começar em 3 passos.

---

## ✅ Passo 1: Setup (Executar uma vez)

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

**Isso vai:**
- Criar arquivo `.env`
- Gerar chave da aplicação
- Configurar banco de dados `futzin`
- Instalar todas as dependências (PHP + npm)
- Rodar migrations
- Popular dados de teste
- Gerar assets

**Tempo estimado**: 5-10 minutos (depende da internet)

---

## ✅ Passo 2: Inicie o Servidor

### Terminal 1 - Backend (Laravel)

**Windows:**
```bash
cd d:\ProjetosWeb\futzin
php artisan serve
```

**Mac/Linux:**
```bash
cd ~/projetos/futzin
php artisan serve
```

Você verá:
```
Starting Laravel development server: http://127.0.0.1:8000
```

Se precisar recompilar CSS/JS após alterações de frontend:
```bash
npm run build
```

---

## ✅ Passo 3: Acesse o Sistema

Abra no navegador: **http://localhost:8000**

### Faça Login

```
Email: admin@futzin.com
Senha: password
```

---

## 🎯 Pronto!

Você está dentro do Futzin! Explore:

- **Dashboard** - Visão geral
- **Grupos** - Seu grupo "Futebol da Terça"
- **Ranking** - Veja os rankings dos jogadores
- **Assinatura** - Seu plano Premium
- **Pagamentos** - Histórico de mensalidades

---

## 💡 Atalhos Úteis

### Comandos rápidos
```bash
php artisan serve
npm run build
```

---

## 🐛 Problemas?

### "Can't connect to MySQL"
Verifique se MySQL está rodando:
```bash
mysql -u root -p
```

### "Port 8000 already in use"
Use outra porta:
```bash
php artisan serve --port=8001
```

### "npm: command not found"
Instale Node.js: https://nodejs.org

### "php: command not found"
Instale PHP 8.3+: https://www.php.net/downloads

---

## 📚 Documentação

- [README.md](./README.md) - Guia completo
- [MYSQL_SETUP.md](./MYSQL_SETUP.md) - Setup MySQL
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Arquitetura

---

**Tudo pronto! Comece agora! ⚽**
