# 🚀 Guía de Despliegue Local

Esta guía te permite probar la plataforma EdTech en tu máquina local.

## ⚡ Opción Rápida (Recomendada)

Ejecutar el script automático:

```bash
cd /ruta/al/proyecto/edtech-platform
chmod +x setup-local.sh
./setup-local.sh
```

El script hará todo automáticamente y abrirá las URLs en tu navegador.

---

## 🔧 Instalación Manual

### 1. Requisitos Previos

Asegúrate de tener instalado:
- PHP 8.3+
- Composer
- Node.js 20+
- PostgreSQL (o usa Docker)

### 2. Backend (Laravel)

```bash
cd backend

# Instalar dependencias
composer install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos SQLite (para pruebas rápidas)
touch database/database.sqlite
# Editar .env y cambiar:
# DB_CONNECTION=sqlite
# DB_DATABASE=/ruta/absoluta/backend/database/database.sqlite

# O usar PostgreSQL
# DB_CONNECTION=pgsql
# DB_HOST=localhost
# DB_PORT=5432
# DB_DATABASE=edtech
# DB_USERNAME=postgres
# DB_PASSWORD=tu_password

# Ejecutar migraciones
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

Backend disponible en: http://localhost:8000

### 3. Frontend (Next.js)

```bash
cd frontend

# Instalar dependencias
npm install

# Configurar API URL
echo "NEXT_PUBLIC_API_URL=http://localhost:8000/api" > .env.local

# Iniciar desarrollo
npm run dev
```

Frontend disponible en: http://localhost:3000

---

## 🐳 Usar Docker (Más fácil)

Si tienes Docker instalado:

```bash
# En la raíz del proyecto
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener
docker-compose down
```

Servicios levantados:
- App Laravel: http://localhost:8000
- Frontend: http://localhost:3000
- PostgreSQL: localhost:5432
- Redis: localhost:6379

---

## 🔑 Credenciales de Prueba

### Admin Panel
- URL: http://localhost:3000/admin/login
- Email: `admin@edtech.com`
- Password: `admin123`

### Usuario Regular
- Registrarse en: http://localhost:3000/register
- O usar el seeder que crea usuarios de prueba

---

## 📁 Estructura de Pantallas

| URL | Descripción |
|-----|-------------|
| `/login` | Login de estudiantes/instructores |
| `/register` | Registro de nuevos usuarios |
| `/admin/login` | Login de administradores |
| `/admin/dashboard` | Gestión de instructores |
| `/instructor/dashboard` | Panel del instructor |
| `/instructor/upload` | Subir archivos y grabar voz |

---

## ⚠️ Notas Importantes

### Para pruebas SIN APIs externas:
- Las respuestas de IA mostrarán mensajes simulados
- La grabación de voz funciona pero no se procesa con ElevenLabs
- Los uploads de archivos son visuales (no se guardan en storage real)

### Para activar APIs reales:
Editar `backend/.env` y agregar:
```
OPENROUTER_API_KEY=tu_key_aqui
ELEVENLABS_API_KEY=tu_key_aqui
OPENAI_API_KEY=tu_key_aqui
TELEGRAM_BOT_TOKEN=tu_token_aqui
```

---

## 🛠️ Solución de Problemas

### Error: "No such file or directory"
```bash
# Asegúrate de estar en la carpeta correcta
pwd  # Debe terminar en edtech-platform
```

### Error: "Permission denied"
```bash
chmod +x setup-local.sh
```

### Error: "Port already in use"
```bash
# Cambiar puertos en .env o matar procesos
lsof -ti:8000 | xargs kill -9
lsof -ti:3000 | xargs kill -9
```

### Error de CORS
El backend ya tiene configurado CORS, pero si hay problemas:
```bash
# En backend/.env
APP_URL=http://localhost:3000
FRONTEND_URL=http://localhost:3000
```

---

## ✅ Verificación

Después de ejecutar el script, verifica:

1. ✅ Backend responde: http://localhost:8000
2. ✅ Frontend carga: http://localhost:3000
3. ✅ Admin login: http://localhost:3000/admin/login
4. ✅ Instructor dashboard: http://localhost:3000/instructor/dashboard
5. ✅ Upload page: http://localhost:3000/instructor/upload

---

¿Problemas? Revisa los logs:
```bash
# Backend
cd backend && php artisan serve

# Frontend  
cd frontend && npm run dev
```
