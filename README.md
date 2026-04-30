# 🎯 Futzin - Sistema SaaS de Futebol

Um sistema completo para gestão de partidas de futebol, ranking de jogadores e controle financeiro com estilo Cartola FC.

## 🚀 Funcionalidades Principais

### 👥 Tipos de Usuários
- **Organizador (Admin)**: Gerencia grupos, mensalidades, presença, times, partidas, resultados e assinaturas
- **Jogador**: Participa de grupos, confirma presença, avalia jogadores, vota MVP e acompanha rankings

### ⚽ Funcionalidades
- ✅ Gestão de partidas
- ✅ Sistema de ranking baseado em desempenho
- ✅ Avaliação de jogadores (1-10)
- ✅ Votação de MVP
- ✅ Sistema de penalidades (cartões amarelos e vermelhos)
- ✅ Controle de mensalidades
- ✅ Sistema de assinatura (Free, Basic, Premium, Enterprise)

## 📋 Cálculo de Ranking

```
Pontuação final = (média de notas das enquetes × peso_nota)
				+ (votos MVP recebidos × peso_mvp)
				+ (vitórias × peso_vitória)
				+ (penalidades negativas × peso_penalidade)

Penalidades:
- Cartão amarelo: -1 ponto
- Cartão vermelho: -3 pontos
```

## 💰 Planos de Assinatura

| Plano | Preço | Recursos |
|-------|-------|----------|
| Free | Grátis | Visualizar rankings |
| Basic | R$ 29,90/mês | Participar de grupos, avaliações |
| Premium | R$ 59,90/mês | Criar grupos, gerenciar partidas |
| Enterprise | R$ 99,90/mês | Suporte prioritário, API access |

## 🛠️ Stack Tecnológico

### Backend
- **Laravel 13** com API REST
- **Laravel Sanctum** para autenticação
- **MySQL** (banco de dados)
- **PHP 8.3+**

### Frontend
- **Laravel Blade** (fullstack server-rendered)
- **Tailwind CSS 4** para estilos
- **Vite** para build de assets

## 📦 Requisitos

- PHP 8.3+
- Node.js 18+
- npm ou yarn
- **MySQL 5.7+** ou **MySQL 8.0+**
- Composer

## 🚀 Instalação Rápida

## 🚂 Deploy na Railway

Este projeto já está preparado para Railway com os arquivos:
- `railway.json`
- `nixpacks.toml`

### Passo a passo

1. Crie um projeto na Railway e conecte este repositório.
2. Adicione um serviço MySQL na Railway.
3. Configure as variáveis de ambiente no serviço da aplicação (base: `.env.railway.example`):

```env
APP_NAME=Futzin
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...   # gerar com php artisan key:generate --show
APP_URL=https://SEU_DOMINIO.railway.app

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=SEU_HOST_MYSQL_RAILWAY
DB_PORT=3306
DB_DATABASE=SEU_DB
DB_USERNAME=SEU_USER
DB_PASSWORD=SUA_SENHA

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

4. Faça o deploy inicial.
5. Após o primeiro deploy, rode as migrations no shell da Railway:

```bash
php artisan migrate --force
```

Opcional (seed de dados):

```bash
php artisan db:seed --force
```

Observação: o start command usa `php artisan serve` escutando em `0.0.0.0:$PORT`, compatível com Railway.

### ⚠️ Pré-requisito: MySQL

**Windows:**
Instale MySQL: https://dev.mysql.com/downloads/mysql/

**Mac:**
```bash
brew install mysql
brew services start mysql
```

**Linux (Ubuntu):**
```bash
sudo apt-get install mysql-server
sudo systemctl start mysql
```

### Manual
```bash
# Setup Laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed

# Setup Frontend
npm install
npm run build

# Subir app local
php artisan serve
```

Para logs avançados com Laravel Pail (somente Linux/macOS com `pcntl`):

```bash
composer dev:with-logs
```

## 🎮 Usando

- **Aplicação**: http://localhost:8000

**Credenciais de teste:**
- Email: `admin@futzin.com`
- Senha: `password`

---

## 📚 Documentação Completa

- [QUICK_START.md](./QUICK_START.md) - Primeiros passos (recomendado!)
- [MYSQL_SETUP.md](./MYSQL_SETUP.md) - Configuração MySQL
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Arquitetura técnica
- [VALIDATION_CHECKLIST.md](./VALIDATION_CHECKLIST.md) - Testes e validação

---

## 📁 Estrutura

```
futzin/
├── app/Http/Controllers/Api/     # Controllers da API
├── app/Models/                    # Modelos Eloquent
├── database/migrations/           # Migrations
├── database/seeders/              # Seeders
├── resources/js/                  # Frontend Vue.js
│   ├── stores/                    # Pinia stores
│   ├── pages/                     # Páginas
│   └── services/                  # API client
├── routes/api.php                 # Rotas da API
└── routes/web.php                 # Rotas web
```

## 🔌 API Endpoints

### Autenticação
```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me
```

### Grupos, Partidas, Rankings e Assinatura
```
GET/POST/PUT/DELETE /api/groups
POST /api/matches
GET /api/rankings/group/{groupId}
GET/POST /api/subscriptions
```

## 📝 License

MIT

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
