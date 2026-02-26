# EdTech Platform

Plataforma educativa con IA generativa para aprendizaje personalizado.

## 🚀 Características

- **IA Conversacional**: Chat con RAG (Retrieval Augmented Generation)
- **Voice Cloning**: Voces personalizadas de instructores vía ElevenLabs
- **Desafíos Diarios**: 6 tipos de desafíos generados automáticamente
- **Spaced Repetition**: Algoritmo SM-2 para repaso óptimo
- **Recordatorios Inteligentes**: Detección de inactividad, recordatorios de examen
- **Gamificación**: Puntos, niveles, streaks, logros, leaderboard
- **Multi-canal**: Notificaciones por Telegram, WhatsApp, Email

## 🏗️ Arquitectura

```
Hexagonal Architecture (Ports & Adapters)
├── Domain/           # Entidades, Value Objects, Interfaces
├── Application/      # Casos de uso, Servicios
├── Infrastructure/   # Adaptadores (Eloquent, APIs externas)
└── Http/            # Controllers, Routes
```

## 🛠️ Stack Tecnológico

### Backend
- Laravel 11 + PHP 8.3
- PostgreSQL 16
- Redis (caché, colas)
- OpenRouter (LLMs)
- ElevenLabs (Voice Cloning)
- OpenAI (Embeddings)

### Frontend
- Next.js 14 + TypeScript
- Tailwind CSS
- React Query
- Zustand (state management)

## 📦 Instalación

### Requisitos
- Docker & Docker Compose
- Git

### Local Development

```bash
# Clonar repositorio
git clone https://github.com/mauromauromauromaurotoranzo-ctrl/edtech-platform.git
cd edtech-platform

# Configurar variables de entorno
cp backend/.env.example backend/.env

# Iniciar servicios
docker-compose up -d

# Instalar dependencias
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate

# Frontend
cd frontend
npm install
npm run dev
```

## 🔧 Configuración

Variables de entorno necesarias en `backend/.env`:

```env
# Database
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=edtech
DB_USERNAME=postgres
DB_PASSWORD=secret

# APIs
OPENROUTER_API_KEY=your_key
ELEVENLABS_API_KEY=your_key
OPENAI_API_KEY=your_key

# Telegram (opcional)
TELEGRAM_BOT_TOKEN=your_token
```

## 🧪 Testing

```bash
# Backend tests
docker-compose exec app php artisan test

# Frontend tests
cd frontend && npm test
```

## 📚 API Endpoints

### Auth
- `POST /api/register` - Registro
- `POST /api/login` - Login
- `GET /api/me` - Perfil usuario

### Chat
- `POST /api/chat` - Enviar mensaje
- `GET /api/conversations` - Listar conversaciones

### Challenges
- `GET /api/challenge/daily` - Desafío del día
- `POST /api/challenge/answer` - Responder desafío
- `GET /api/leaderboard` - Tabla de líderes

## 🚀 Deploy

```bash
# Producción
./deploy.sh
```

## 📄 Licencia

MIT License
