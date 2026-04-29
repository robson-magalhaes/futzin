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
Pontuação final = (média de notas × 2) + (gols × 3) + (assistências × 1.5) + (MVPs × 5) + penalidades

Penalidades:
- Cartão amarelo: -1 ponto
- Cartão vermelho: -3 pontos + status de expulso
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
- **Vue.js 3** (SPA)
- **Vue Router 4** para navegação
- **Pinia** para gerenciamento de estado
- **Axios** para requisições HTTP
- **Tailwind CSS 4** para estilos

## 📦 Requisitos

- PHP 8.3+
- Node.js 18+
- npm ou yarn
- **MySQL 5.7+** ou **MySQL 8.0+**
- Composer

## 🚀 Instalação Rápida

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

### Setup Automático

#### Windows
```bash
.\setup.bat
```

#### Linux/Mac
```bash
chmod +x setup.sh
./setup.sh
```

**O script vai:**
- Criar banco de dados `futzin`
- Instalar dependências PHP e npm
- Rodar migrations
- Popular dados de teste

### Manual
```bash
# Criar banco de dados
mysql -u root -p < create_database.sql

# Setup Laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed

# Setup Frontend
npm install
npm run build
composer dev
```

Para logs avançados com Laravel Pail (somente Linux/macOS com `pcntl`):

```bash
composer dev:with-logs
```

## 🎮 Usando

- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api

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
