# 🏗️ Arquitetura Técnica - Futzin

## Diagrama de Arquitetura

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT BROWSER                           │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │              Vue 3 SPA (Port 5173)                         │ │
│  ├────────────────────────────────────────────────────────────┤ │
│  │ • Vue Router (Routing)                                     │ │
│  │ • Pinia Stores (State Management)                          │ │
│  │ • Tailwind CSS (Styling)                                   │ │
│  │ • Axios HTTP Client                                        │ │
│  └────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────┘
                           ↓ HTTP/REST
┌─────────────────────────────────────────────────────────────────┐
│                    API SERVER (Port 8000)                       │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │         Laravel 13 + Laravel Sanctum (Auth)               │ │
│  ├────────────────────────────────────────────────────────────┤ │
│  │ Routes                                                      │ │
│  │  └─ /api/auth/*          (AuthController)                 │ │
│  │  └─ /api/groups/*        (GroupController)                │ │
│  │  └─ /api/matches/*       (MatchController)                │ │
│  │  └─ /api/ratings/*       (RatingController)               │ │
│  │  └─ /api/rankings/*      (RankingController)              │ │
│  │  └─ /api/subscriptions/* (SubscriptionController)         │ │
│  │  └─ /api/payouts/*       (PayoutController)               │ │
│  └────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────┘
                           ↓ SQL
┌─────────────────────────────────────────────────────────────────┐
│                   DATABASE (SQLite/MySQL)                       │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ Tables:                                                     │ │
│  │ ├─ users                                                   │ │
│  │ ├─ groups                                                  │ │
│  │ ├─ user_groups (belongsToMany)                            │ │
│  │ ├─ matches                                                 │ │
│  │ ├─ teams                                                   │ │
│  │ ├─ player_match (belongsToMany)                           │ │
│  │ ├─ player_penalties                                        │ │
│  │ ├─ player_ratings                                          │ │
│  │ ├─ player_rankings                                         │ │
│  │ └─ payouts                                                 │ │
│  │ └─ subscriptions                                           │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📦 Estrutura de Pastas

```
futzin/
│
├── 📱 Frontend (resources/)
│   ├── js/
│   │   ├── app.js                 # Entrada Vue
│   │   ├── App.vue                # Layout raiz
│   │   ├── bootstrap.js           # Configuração
│   │   │
│   │   ├── stores/                # Pinia stores
│   │   │   ├── auth.js            # Autenticação
│   │   │   ├── group.js           # Grupos
│   │   │   ├── match.js           # Partidas
│   │   │   ├── ranking.js         # Rankings
│   │   │   └── subscription.js    # Assinaturas
│   │   │
│   │   ├── pages/                 # Páginas (Views)
│   │   │   ├── Login.vue
│   │   │   ├── Register.vue
│   │   │   ├── Dashboard.vue
│   │   │   ├── Groups.vue
│   │   │   ├── GroupDetail.vue
│   │   │   ├── Ranking.vue
│   │   │   ├── Subscription.vue
│   │   │   ├── PaymentHistory.vue
│   │   │   └── Matches*.vue       # (Stubs)
│   │   │
│   │   ├── services/
│   │   │   └── api.js             # Axios HTTP client
│   │   │
│   │   └── components/            # (Para futura expansão)
│   │
│   ├── css/
│   │   └── app.css                # Tailwind
│   │
│   └── views/
│       └── app.blade.php          # Template SPA
│
├── 🔌 Backend (app/)
│   ├── Http/
│   │   ├── Controllers/Api/       # API Controllers
│   │   │   ├── AuthController.php
│   │   │   ├── GroupController.php
│   │   │   ├── MatchController.php
│   │   │   ├── RatingController.php
│   │   │   ├── RankingController.php
│   │   │   ├── SubscriptionController.php
│   │   │   └── PayoutController.php
│   │   │
│   │   ├── Middleware/
│   │   │   └── EnsureSubscriptionIsActive.php
│   │   │
│   │   └── Controllers/          # (Legacy, não usado)
│   │
│   ├── Models/                    # Eloquent Models
│   │   ├── User.php
│   │   ├── Group.php
│   │   ├── Match.php
│   │   ├── Team.php
│   │   ├── PlayerMatch.php
│   │   ├── PlayerPenalty.php
│   │   ├── PlayerRating.php
│   │   ├── Subscription.php
│   │   ├── PlayerRanking.php
│   │   └── Payout.php
│   │
│   └── Policies/                  # Authorization
│       ├── GroupPolicy.php
│       └── UserPolicy.php
│
├── 🗄️ Database (database/)
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php        (original)
│   │   ├── 0001_01_01_000001_create_cache_table.php        (original)
│   │   ├── 0001_01_01_000002_create_jobs_table.php         (original)
│   │   ├── 2026_04_25_000003_create_groups_table.php       ✨ NEW
│   │   ├── 2026_04_25_000004_create_user_groups_table.php  ✨ NEW
│   │   ├── 2026_04_25_000005_create_matches_table.php      ✨ NEW
│   │   ├── 2026_04_25_000006_create_teams_table.php        ✨ NEW
│   │   ├── 2026_04_25_000007_create_player_match_table.php ✨ NEW
│   │   ├── 2026_04_25_000008_create_player_penalties_table.php ✨ NEW
│   │   ├── 2026_04_25_000009_create_player_ratings_table.php ✨ NEW
│   │   ├── 2026_04_25_000010_create_subscriptions_table.php ✨ NEW
│   │   ├── 2026_04_25_000011_create_player_rankings_table.php ✨ NEW
│   │   └── 2026_04_25_000012_create_payouts_table.php      ✨ NEW
│   │
│   ├── factories/
│   │   └── UserFactory.php
│   │
│   └── seeders/
│       └── DatabaseSeeder.php     # Popula dados de teste
│
├── 📍 Routes (routes/)
│   ├── api.php                    # API routes (~20 endpoints)
│   ├── web.php                    # Web routes (SPA fallback)
│   └── console.php
│
├── ⚙️ Config (config/)
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── ... (etc)
│
├── 📦 Dependencies
│   ├── package.json               # npm packages (Vue, Pinia, etc)
│   ├── composer.json              # PHP packages (Laravel, Sanctum)
│   └── npm-shrinkwrap.json
│
├── 🔧 Configuration
│   ├── vite.config.js             # Vite bundler config
│   ├── tailwind.config.js
│   ├── phpunit.xml
│   └── artisan                    # Laravel CLI
│
├── 📚 Documentation
│   ├── README.md                  # Documentação completa
│   ├── QUICK_START.md             # Guia rápido
│   ├── DELIVERY_SUMMARY.md        # Sumário de entrega
│   └── ARCHITECTURE.md            # Este arquivo
│
├── 🚀 Setup de Ambiente
│   ├── .env.example               # Template de ambiente
│   ├── README.md                  # Passo a passo manual
│   └── QUICK_START.md             # Guia rápido
│
└── public/
    ├── index.php                  # Laravel entry point
    └── build/                     # Vite build output
```

---

## 🔐 Fluxo de Autenticação

```
1. Usuario registra/loga
   └─ POST /api/auth/register ou /api/auth/login
   └─ Laravel cria token com Sanctum
   └─ Token salvo em localStorage (frontend)

2. Requisições subsequentes
   └─ Header: Authorization: Bearer {token}
   └─ Middleware valida token
   └─ auth:sanctum middleware protege rotas

3. Logout
   └─ Token deletado do banco
   └─ localStorage limpo
```

---

## 📊 Fluxo de Dados - Exemplo: Criar Grupo

```
┌─ Frontend (Vue)
│  ├─ User submete form em GroupsPage.vue
│  └─ groupStore.createGroup(data) chamado
│
└─ Pinia Store (group.js)
   ├─ Chama api.post('/groups', data)
   └─ Retorna response
   
     └─ HTTP Request
        └─ POST /api/groups
        └─ Header: Authorization: Bearer token
        └─ Body: { name, description, monthly_fee }
        
        └─ Backend (Laravel)
           ├─ Route: groups.store
           ├─ Controller: GroupController@store
           ├─ Validação: name, description, monthly_fee
           ├─ Cria novo Group
           ├─ Attach usuário como admin
           └─ Retorna group com status 201
        
        └─ HTTP Response (201 Created)
        └─ Body: { id, user_id, name, ... }
        
   └─ Store atualiza state
   └─ Vue reactivity atualiza UI
   
└─ Frontend exibe novo grupo na listagem
```

---

## 🔄 Ciclo de Vida: Calcular Ranking

```
Quando: Partida finalizada (POST /api/matches/:id/finish)

1. Receber resultado
   ├─ Goals, assists de cada jogador
   ├─ Ratings (avaliações 1-10)
   ├─ Penalties (cartões)
   └─ MVP votes

2. Validar
   └─ Validar dados do formulário

3. Salvar dados
   ├─ Atualizar player_match com ratings
   ├─ Salvar penalties
   └─ Marcar match como finished

4. Calcular ranking (MatchController@calculateRankings)
   ├─ Para cada jogador do grupo:
   │  ├─ Soma média de notas
   │  ├─ Soma gols × 3
   │  ├─ Soma assists × 1.5
   │  ├─ Conta MVPs × 5
   │  ├─ Soma penalidades
   │  └─ Total Score = tudo acima
   │
   ├─ Order rankings por total_score DESC
   ├─ Atualizar posição (1, 2, 3...)
   └─ Salvar em player_rankings

5. Retornar
   └─ Response com match atualizado e rankings
```

---

## 📱 Componentes Vue

### App.vue (Root Layout)
```
┌─────────────────────────────────┐
│         Header (Logo)           │
├─────────────────────────────────┤
│ Dashboard │ Grupos │ Assinatura │
│                   [User Dropdown]│
├─────────────────────────────────┤
│                                  │
│   <router-view>                 │
│   (Página atual)                │
│                                  │
├─────────────────────────────────┤
└─────────────────────────────────┘
```

### Login.vue
```
┌──────────────────────┐
│ Login Form           │
├──────────────────────┤
│ Email: [_________]   │
│ Password: [______]   │
│                      │
│ [Login] [Register]   │
└──────────────────────┘
```

### Ranking Table
```
┌────────────────────────────────────────────────┐
│ Pos │ Jogador │ Partidas │ Gols │ Assist │ Pts │
├────────────────────────────────────────────────┤
│ 1º  │ João    │ 5        │ 8    │ 3      │42.5 │
│ 2º  │ Maria   │ 4        │ 6    │ 2      │38.2 │
│ 3º  │ Pedro   │ 5        │ 4    │ 5      │35.8 │
└────────────────────────────────────────────────┘
```

---

## 🔗 Relacionamentos de Banco

```
users
  ├─ 1:N → groups (user_id)
  ├─ 1:N → subscriptions (user_id)
  ├─ 1:N → player_penalties (user_id)
  ├─ 1:N → player_ratings (user_id, rated_by)
  ├─ 1:N → player_rankings (user_id)
  ├─ 1:N → payouts (user_id)
  └─ N:N → groups via user_groups

groups
  ├─ 1:N → matches (group_id)
  ├─ 1:N → player_rankings (group_id)
  ├─ 1:N → payouts (group_id)
  └─ N:N → users via user_groups

matches
  ├─ 1:N → teams (match_id)
  ├─ 1:N → player_match (match_id)
  ├─ 1:N → player_penalties (match_id)
  └─ 1:N → player_ratings (match_id)

teams
  └─ 1:N → player_match (team_id)

player_match
  ├─ N:1 ← users (user_id)
  ├─ N:1 ← matches (match_id)
  └─ N:1 ← teams (team_id)
```

---

## 🚀 Deployment Ready

Este projeto pode ser deployado em:

**Frontend:**
- Vercel
- Netlify
- GitHub Pages
- AWS S3 + CloudFront

**Backend:**
- Heroku
- Railway
- Render
- AWS EC2
- DigitalOcean

**Database:**
- AWS RDS
- Railway PostgreSQL
- PlanetScale (MySQL)
- Railway SQLite

---

## ✅ Checklist de Features

- [x] Autenticação completa
- [x] Gestão de grupos
- [x] CRUD de partidas
- [x] Sistema de ranking
- [x] Avaliações
- [x] Assinaturas
- [x] Controle de mensalidades
- [x] Penalidades (estrutura)
- [ ] MVP voting UI
- [ ] Integração de pagamento
- [ ] Notificações em tempo real
- [ ] Admin dashboard
- [ ] Relatórios PDF
- [ ] Mobile app nativa

---

**Arquitetura pronta para escalar! 🚀**
