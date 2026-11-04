# 🎵 Spotify Profile Analyzer

Plataforma web profissional e de alta fidelidade para análise de hábitos musicais, descoberta de identidade sonora e geração inteligente de playlists personalizadas, integrada com a **Spotify Web API** através do fluxo **OAuth 2.0 Authorization Code Flow**.

Desenvolvido com arquitetura moderna, modular e desacoplada: **Laravel 12/13 (PHP 8.4)** no backend e **React 19 + TypeScript + Tailwind CSS** no frontend.

---

## 🌟 Funcionalidades Principais

### 1. 🚀 Landing Page Moderna
- Identidade visual escura (*Deep Black* `#0a0a0a` & *Spotify Green* `#1DB954`) com gradientes sutis e efeitos glow.
- Botão principal **"Analisar meu Spotify"** com redirecionamento OAuth 2.0 seguro.
- Botão **"Ver Modo Demo"** para experimentar a aplicação imediatamente sem necessidade de credenciais.
- Seções explicativas de arquitetura, metodologia e segurança.

### 2. 📊 Dashboard Pessoal
- Saudação dinâmica conforme o horário do dia (*"Bom dia", "Boa tarde", "Boa noite"*).
- Foto de perfil oficial, nome, país e status de assinatura (*Premium/Free*).
- Cards com indicadores consolidados:
  - Total de artistas e faixas analisadas.
  - Gênero predominante no perfil.
  - Sua década musical predominante.
  - Artista favorito (*#1 mais escutado*) com gêneros e popularidade.
  - Música mais recorrente com link direto para reprodução no Spotify.
  - Prévia rápida dos rankings e carrossel de insights.

### 3. 🎤 Análise de Artistas
- Ranking dos artistas mais escutados com fotos em alta resolução.
- Seletor de período aceito oficialmente pela API:
  - **Últimas 4 semanas** (`short_term`)
  - **Últimos 6 meses** (`medium_term`)
  - **Todos os tempos / Longo prazo** (`long_term`)
- Pódio visual com destaque para o Top 3 (#1 Ouro, #2 Prata, #3 Bronze).
- Indicador de popularidade global (0-100) e gêneros associados.

### 4. 🎶 Análise de Músicas
- Ranking das músicas mais ouvidas no período.
- Capa do álbum, nome da faixa, artistas e álbum.
- **Classificação automática por década de lançamento**.
- Duração formatada (`MM:SS`) e link para abrir e reproduzir diretamente no player do Spotify.

### 5. 🧭 Análise de Gêneros Musicais & "Gênero Mais Obscuro"
- Gráfico de barras horizontais com os gêneros mais frequentes.
- Gráfico de pizza interativo (*PieChart*) com fatias de concentração sonora.
- Cálculo de **Índice de Diversidade Musical** (relação entre gêneros únicos e repetições).
- **Spotlight: "Seu Gênero Mais Obscuro"**: heurística que identifica o gênero mais raro presente nos seus próprios artistas analisados, com indicação de qual artista o representa.

### 6. 📅 Sua Década Musical
- Linha do tempo cronológica com a distribuição das suas músicas por década (dos anos 1960 aos anos 2020).
- Identificação da sua década predominante.
- **Música mais marcante de cada década**: identifica a faixa de maior popularidade que você ouve para cada era.

### 7. ⏱️ Histórico Recente
- Linha do tempo das faixas reproduzidas recentemente com timestamps relativos (*"há 15 minutos"*).
- **Aviso de transparência da API**: esclarece que a Spotify Web API oferece uma janela restrita das últimas ~50 reproduções, não o histórico vitalício completo da conta.

### 8. ✨ Insights Determinísticos ("Seu Perfil Musical")
- Diagnósticos objetivos gerados por regras lógicas baseadas em números reais:
  - Foco ou ecletismo de gêneros.
  - Concentração ou rotatividade de artistas nas sessões recentes.
  - Fidelidade temporal (década preferida).
  - Sem simulações ou traços psicológicos inventados.

### 9. 🎧 Gerador de Playlists por Clima ("Monte uma playlist para o seu momento")
- Seleção de 9 climas contextuais:
  - 🌧️ **Chuva** (Indie acústico, lo-fi, melancolia calma)
  - 🎉 **Festa** (Pop, dance, bangers eletrônicos)
  - 🎯 **Foco** (Instrumental, ambient, concentração)
  - 😌 **Relaxar** (Chill, bossa nova, jazz)
  - 💭 **Nostalgia** (Clássicos da sua década favorita)
  - 💪 **Treino** (Alta energia, hip-hop, rock pesado)
  - 🚗 **Viagem** (Road trip, pop rock, aventura)
  - 🌙 **Madrugada** (R&B, neo-soul, nocturne)
  - ❤️ **Romântico** (Baladas românticas, love songs)
- **Algoritmo inteligente próprio**: não depende do endpoint `/recommendations` (descontinuado pela Spotify para novos apps). Cruza os gêneros do usuário com buscas otimizadas na API do Spotify.
- Seletor de quantidade de músicas (10, 20, 30, 50 faixas).
- Visualização e conferência da prévia completa antes de salvar.
- Personalização de Nome, Descrição e Visibilidade (Pública/Privada).
- **Salva diretamente na conta do usuário no Spotify** via API e disponibiliza link direto.

### 10. 🎪 Modo Demonstração Completo
- Permite que avaliadores, recrutadores e usuários sem conta no Spotify explorem todas as telas e funcionalidades.
- Dados realistas simulando uma usuária brasileira (*Maria Clara*), claramente sinalizados com banner e flag `is_demo: true`.

---

## 🛠️ Stack Tecnológica

| Camada | Tecnologias |
|---|---|
| **Backend** | PHP 8.4, Laravel 12/13, Eloquent ORM, Laravel HTTP Client, SQLite (local) / PostgreSQL 16 (Docker) |
| **Frontend** | React 19, TypeScript, Vite, Tailwind CSS, TanStack Query v5, Axios, Recharts, Lucide React, Framer Motion |
| **Infraestrutura** | Docker, Docker Compose, Nginx, Alpine Linux |
| **Testes** | PHPUnit 12 (23 testes automatizados cobrindo Auth, Services, Analytics e Playlists) |

---

## 🔒 Segurança & Boas Práticas

- **OAuth 2.0 Authorization Code Flow**: Client Secret armazenado exclusivamente no backend.
- **Validação de State**: Tokens CSRF criptograficamente seguros em cada requisição de autorização.
- **Criptografia de Tokens**: `access_token` e `refresh_token` são armazenados com criptografia simétrica no banco de dados (`casts => ['access_token' => 'encrypted']`).
- **Renovação Automática**: Tokens de acesso são renovados automaticamente via *refresh token* com buffer de 5 minutos antes da expiração.
- **Sem Vazamento de Segredos**: Tokens de acesso nunca são expostos no frontend ou gravados em arquivos de log.
- **Rate Limit & Retry-After**: Tratamento de respostas HTTP 429 da Spotify Web API respeitando o cabeçalho `Retry-After`.
- **Desconexão Total**: Endpoint `DELETE /api/spotify/disconnect` revoga e remove tokens do banco e encerra a sessão.

---

## 📂 Estrutura de Diretórios

```
Spotify-Analytics/
├── backend/                              # Aplicação Laravel
│   ├── app/
│   │   ├── Enums/                        # TimeRange, MoodCategory
│   │   ├── Exceptions/                   # SpotifyApiException centralizada
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Analytics/            # Artists, Tracks, Genres, Decades, RecentlyPlayed, Insights
│   │   │   │   ├── Auth/                 # SpotifyAuthController
│   │   │   │   ├── Dashboard/            # DashboardController
│   │   │   │   ├── Demo/                 # DemoController (dados fictícios)
│   │   │   │   └── Playlist/             # PlaylistController
│   │   │   ├── Middleware/               # EnsureSpotifyConnected
│   │   │   └── Requests/                 # GeneratePlaylistRequest, CreatePlaylistRequest
│   │   ├── Models/                       # User, SpotifyToken, AnalyticsSnapshot, GeneratedPlaylist
│   │   └── Services/
│   │       ├── Analytics/                # MusicAnalytics, GenreAnalysis, DecadeAnalysis, Insights
│   │       ├── Playlist/                 # PlaylistGeneratorService
│   │       └── Spotify/                  # SpotifyAuthService, SpotifyApiService, SpotifyTokenService
│   ├── config/                           # spotify.php, cors.php
│   ├── database/migrations/              # Migrações do banco
│   ├── routes/                           # api.php, web.php
│   └── tests/                            # Feature e Unit tests (PHPUnit)
├── frontend/                             # Aplicação React + Vite + TypeScript
│   ├── src/
│   │   ├── api/                          # Axios client e endpoints estruturados
│   │   ├── components/
│   │   │   ├── layout/                   # Navbar, Sidebar, AppLayout
│   │   │   └── ui/                       # StatCard, LoadingSkeleton, ErrorMessage, TimeRangeSelector
│   │   ├── contexts/                     # AuthContext (OAuth e Modo Demo)
│   │   ├── pages/                        # Landing, Dashboard, Artists, Tracks, Genres, Decades, etc.
│   │   └── types/                        # Tipagens completas TypeScript
│   ├── index.html
│   ├── tailwind.config.js
│   └── vite.config.ts
├── docker-compose.yml                    # Orquestração com PostgreSQL, Nginx e PHP
├── .env.example                          # Modelo de variáveis de ambiente
└── README.md
```

---

## ⚙️ Configuração do Spotify Developer Dashboard

Para utilizar com credenciais reais do Spotify:

1. Acesse o [Spotify Developer Dashboard](https://developer.spotify.com/dashboard) e faça login.
2. Clique em **"Create App"**.
3. Preencha:
   - **App name**: `Spotify Profile Analyzer`
   - **App description**: `Análise pessoal de catálogo musical e gerador de playlists`
   - **Redirect URI**: `http://localhost:8000/api/spotify/callback`
   - **Which API/SDKs are you planning to use?**: Marque **Web API**.
4. Aceite os termos e salve.
5. Em **Settings**, copie o **Client ID** e o **Client Secret**.
6. Em **Users and Access**, adicione seu e-mail do Spotify à lista de usuários de teste (obrigatório para apps em *Development Mode*).

---

## 🚀 Como Executar o Projeto

### Opção 1: Execução Local (Rápida com SQLite)

#### Backend:
```bash
cd backend
composer install
cp .env.example .env     # Preencha SPOTIFY_CLIENT_ID e SPOTIFY_CLIENT_SECRET
php artisan key:generate
php artisan migrate
php artisan test         # Executa a suíte de 23 testes
php artisan serve        # Inicia em http://localhost:8000
```

#### Frontend:
```bash
cd frontend
npm install
npm run build            # Verifica tipagem e cria build
npm run dev              # Inicia em http://localhost:5173
```

Acesse **http://localhost:5173** no seu navegador!

---

### Opção 2: Execução com Docker Compose (PostgreSQL + Nginx)

1. Configure as variáveis no `.env`:
```bash
cp .env.example .env
# Adicione seu SPOTIFY_CLIENT_ID e SPOTIFY_CLIENT_SECRET no .env
```

2. Suba todos os contêineres:
```bash
docker-compose up -d --build
```

3. Execute as migrações no contêiner do Laravel:
```bash
docker-compose exec backend php artisan migrate
```

Acesse **http://localhost:5173** para o Frontend e **http://localhost:8000/api** para o Backend!

---

## 🧪 Testes Automatizados

Para executar todos os testes automatizados do backend:

```bash
cd backend
php artisan test
```

### Cobertura dos testes:
- **`AuthTest`**: Validação de state CSRF no OAuth, troca de código por tokens via mock HTTP, persistência segura do usuário e tokens, rota de desconexão e proteção contra acessos não autenticados.
- **`DemoApiTest`**: Validação de todos os endpoints públicos do modo demonstração (Dashboard, Artistas, Músicas, Gêneros, Décadas, Histórico, Insights e Playlists).
- **`SpotifyAnalyticsTest`**: Respostas das análises com mocks HTTP da Spotify API, cálculo de porcentagens de gêneros e categorização por décadas.
- **`SpotifyTokenServiceTest`**: Teste unitário confirmando que os tokens gravados no banco de dados são criptografados e que a renovação automática ocorre de forma transparente.

---

## 📌 Limitações Conhecidas da Spotify Web API (Pós-Novembro 2024)

> [!IMPORTANT]
> A partir de 27 de novembro de 2024, a Spotify implementou restrições severas em sua plataforma de desenvolvedores para novos aplicativos:
> - **`/v1/audio-features` e `/v1/audio-analysis`**: Descontinuados para novas aplicações (retornam 403 Forbidden). Por isso, este projeto não utiliza métricas inexistentes como *danceability*, *valence* ou *energy*.
> - **`/v1/recommendations`**: Descontinuado para novos aplicativos. O gerador de playlists deste projeto foi construído utilizando um **algoritmo próprio** baseado em buscas textuais refinadas (`/v1/search`), gêneros dos top artistas e cruzamento com os hábitos do usuário.
> - **Histórico de Reproduções**: O endpoint `/v1/me/player/recently-played` fornece apenas as últimas ~50 reproduções recentes do player, e não o histórico vitalício da conta.
> - **Modo de Desenvolvimento**: Aplicativos novos no Spotify Developer Dashboard podem autorizar até 5 usuários de teste previamente cadastrados no painel.

---

## 📄 Licença

Distribuído sob a licença MIT. Consulte `LICENSE` para mais informações.

