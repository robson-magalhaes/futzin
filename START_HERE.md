# 🚀 Executar Futzin - Guia Rápido

MySQL já está instalado! Vamos começar em 3 passos.

---

## ✅ Passo 1: Setup (Executar uma vez)

### Windows
```bash
cd d:\ProjetosWeb\futzin
.\setup.bat
```

### Mac/Linux
```bash
cd ~/projetos/futzin
chmod +x setup.sh
./setup.sh
```

**Isso vai:**
- Criar arquivo `.env`
- Gerar chave da aplicação
- Criar banco de dados `futzin`
- Instalar todas as dependências (PHP + npm)
- Rodar migrations
- Popular dados de teste
- Build frontend

**Tempo estimado**: 5-10 minutos (depende da internet)

---

## ✅ Passo 2: Abra 2 Terminais

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

### Terminal 2 - Frontend (Vue)

**Windows:**
```bash
cd d:\ProjetosWeb\futzin
npm run dev
```

**Mac/Linux:**
```bash
cd ~/projetos/futzin
npm run dev
```

Você verá:
```
➜  Local:   http://localhost:5173/
```

---

## ✅ Passo 3: Acesse o Sistema

Abra no navegador: **http://localhost:5173**

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

### Script Rápido (Windows)
```bash
.\start.bat server    # Terminal 1
.\start.bat dev       # Terminal 2
```

### Script Rápido (Mac/Linux)
```bash
./start.sh server     # Terminal 1
./start.sh dev        # Terminal 2
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

### "Port 5173 already in use"
Use outra porta:
```bash
npm run dev -- --port 5174
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
