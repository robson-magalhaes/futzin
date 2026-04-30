# ✅ Checklist de Validação - Futzin

Use este guia para validar que tudo está funcionando corretamente após a instalação.

---

## 1️⃣ Setup Inicial

- [ ] Executou setup manual (`composer install`, `php artisan migrate --force`, `npm run build`)
- [ ] Sem erros de Composer
- [ ] Sem erros de npm
- [ ] Arquivo `.env` foi criado
- [ ] Migrations rodaram com sucesso
- [ ] Seeders popularam o banco

---

## 2️⃣ Backend (API)

### Inicie o servidor
```bash
php artisan serve
```

### Verifique se responde
```bash
curl http://localhost:8000/api/auth/me
```
Deverá retornar erro 401 (esperado, sem token)

### Faça login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@futzin.com","password":"password"}'
```

Você receberá:
```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@futzin.com"
  },
  "token": "seu_token_aqui"
}
```

- [ ] Login retorna token válido
- [ ] User retorna dados corretos

### Use o token para acessar rotas protegidas
```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer seu_token_aqui"
```

Deverá retornar seus dados.

- [ ] /api/auth/me funciona
- [ ] /api/groups funciona
- [ ] /api/rankings/group/1 funciona

---

## 3️⃣ Frontend (Vue)

### Inicie o dev server
```bash
npm run dev
```

Deverá exibir:
```
➜  Local:   http://localhost:5173/
```

### Acesse a aplicação
http://localhost:5173

- [ ] Página de login carrega
- [ ] Sem erros de console

### Faça login
- Email: `admin@futzin.com`
- Senha: `password`

- [ ] Login funciona
- [ ] Redireciona para Dashboard
- [ ] Token salvo em localStorage

### Verifique Dashboard
- [ ] Mostra "Bem-vindo"
- [ ] Exibe subscription "Premium"
- [ ] Mostra "1 grupo"
- [ ] Mostra "0 próximas partidas"

- [ ] Grupo "Futebol da Terça" aparece em Grupos

### Clique em "Grupos"
- [ ] Listagem carrega
- [ ] Vê grupo "Futebol da Terça"
- [ ] Descrição e fee aparecem

### Clique em "Ver Ranking" (dentro do grupo)
- [ ] Tabela de ranking carrega
- [ ] Mostra 10 jogadores
- [ ] Posição, nome, partidas, gols, assists, notas, pontos aparecem
- [ ] Ordenado por pontuação DESC (maior primeiro)

### Clique em "Assinatura"
- [ ] Mostra 3 planos: Basic, Premium (selecionado), Enterprise
- [ ] Seu plano está ativo (Badge "Premium Ativo")
- [ ] Botão "Cancelar Assinatura" aparece

### Clique em "Pagamentos"
- [ ] Tabela de pagamentos carrega
- [ ] Mostra 10 mensalidades (pendentes)
- [ ] Status mostra "Pendente" em laranja
- [ ] Data de vencimento aparece

---

## 4️⃣ API Testing com Postman/Insomnia

### Setup
1. Faça login via `/api/auth/login` para obter token
2. Configure Authentication como "Bearer Token"
3. Cole seu token

### Endpoints para testar

#### Grupos
- [ ] GET /api/groups - Retorna seu grupo
- [ ] POST /api/groups - Cria novo (requer campo `name`)
- [ ] GET /api/groups/{id} - Detalhe do grupo
- [ ] PUT /api/groups/{id} - Atualiza grupo
- [ ] POST /api/groups/{id}/members - Adiciona membro

#### Rankings
- [ ] GET /api/rankings/group/1 - Todos os rankings do grupo
- [ ] GET /api/rankings/group/1/player/2 - Stats de jogador específico

#### Assinatura
- [ ] GET /api/subscriptions/current - Sua assinatura ativa
- [ ] POST /api/subscriptions - Contratar plano
- [ ] POST /api/subscriptions/cancel - Cancelar assinatura

#### Pagamentos
- [ ] GET /api/payouts - Seus pagamentos
- [ ] PUT /api/payouts/{id}/mark-paid - Marcar como pago

---

## 5️⃣ Teste Mobile Responsivo

### No Chrome DevTools
1. Abra http://localhost:5173
2. Pressione `F12`
3. Clique no ícone de dispositivo (Ctrl+Shift+M)
4. Teste em diferentes tamanhos

- [ ] Layout funciona em iPhone 12
- [ ] Layout funciona em iPad
- [ ] Navs ficam compactos em mobile
- [ ] Tabelas scrollam horizontalmente

---

## 6️⃣ Testes de Segurança

### Token expira?
1. Copie token do localStorage
2. Edite-o (mude último caractere)
3. Tente usar em requisição
- [ ] Retorna 401 Unauthorized

### Sem token?
1. Limpe localStorage
2. Tente acessar /api/groups
- [ ] Retorna 401 Unauthorized

### Outro usuário?
1. Crie novo usuário
2. Login com ele
3. Tente acessar grupo de outro usuário
- [ ] Retorna erro ou não mostra os dados

---

## 7️⃣ Testes de Dados

### Verifique Database
```bash
# SQLite
sqlite3 database/database.sqlite

# Ver tabelas
.tables

# Ver users
SELECT * FROM users;

# Ver groups
SELECT * FROM groups;

# Ver rankings
SELECT * FROM player_rankings LIMIT 5;
```

- [ ] users table tem 11 registros (1 admin + 10 players)
- [ ] groups table tem 1 registro
- [ ] player_rankings tem ~10 registros

---

## 8️⃣ Verificação de Arquivos

### Backend
- [ ] `app/Http/Controllers/Api/` contém 7 arquivos
- [ ] `app/Models/` contém 10 arquivos
- [ ] `database/migrations/` contém 12 arquivos
- [ ] `routes/api.php` existe e tem ~20 rotas

### Frontend
- [ ] `resources/js/stores/` contém 5 arquivos
- [ ] `resources/js/pages/` contém 8 arquivos
- [ ] `resources/js/services/api.js` existe
- [ ] `resources/views/app.blade.php` existe

### Config
- [ ] `.env` existe
- [ ] `package.json` tem Vue, Pinia, Tailwind
- [ ] `composer.json` tem Laravel, Sanctum
- [ ] `vite.config.js` existe

---

## 9️⃣ Build para Produção

### Build frontend
```bash
npm run build
```

- [ ] Build completa sem erros
- [ ] Gera pasta `public/build/`
- [ ] CSS e JS minificados

### Build backend
```bash
php artisan optimize
```

- [ ] Sem erros
- [ ] Configura cache

---

## 🔟 Performance

### Frontend
- [ ] Página carrega em < 2 segundos
- [ ] Navegação é suave (sem lag)
- [ ] Scroll é fluido

### API
```bash
time curl http://localhost:8000/api/groups \
  -H "Authorization: Bearer seu_token"
```

- [ ] Response < 100ms
- [ ] Sem N+1 queries

---

## 📋 Final Checklist

- [ ] Setup completou sem erros
- [ ] Backend responde a requisições
- [ ] Frontend carrega e exibe dados
- [ ] Login funciona
- [ ] Logout funciona
- [ ] Dados aparecem em tabelas
- [ ] Navegação funciona
- [ ] Responsivo em mobile
- [ ] Segurança funciona (tokens)
- [ ] Build de produção funciona

---

## ✅ Se tudo passou:

**SISTEMA ESTÁ PRONTO PARA USO! 🎉**

---

## ❌ Se algo falhou:

### Erros comuns e soluções

#### "Porta 8000 já está em uso"
```bash
php artisan serve --port=8001
```

#### "Erro ao conectar com banco"
```bash
php artisan migrate --force
php artisan db:seed
```

#### "npm: command not found"
Instale Node.js de https://nodejs.org

#### "Composer não encontrado"
Instale de https://getcomposer.org

#### "Erro de CORS"
Verifique se backend está na porta 8000 e frontend em 5173

#### "Página em branco"
1. Abra DevTools (F12)
2. Veja aba Console para erros
3. Verifique se API está rodando

#### "Login não funciona"
1. Verifique se seeder rodou: `php artisan db:seed`
2. Verifique credentials: `admin@futzin.com / password`
3. Limpe cache: `php artisan cache:clear`

---

## 📞 Suporte

Veja documentação em:
- README.md
- QUICK_START.md
- DELIVERY_SUMMARY.md
- ARCHITECTURE.md

**Boa sorte! ⚽**
