#!/bin/bash

# 🚀 Script de Setup Local para EdTech Platform
# Este script configura el proyecto para pruebas locales

set -e  # Detenerse en cualquier error

echo "🎓 EdTech Platform - Setup Local"
echo "================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Detectar OS
OS="$(uname -s)"
case "${OS}" in
    Linux*)     PLATFORM=Linux;;
    Darwin*)    PLATFORM=Mac;;
    CYGWIN*|MINGW*|MSYS*) PLATFORM=Windows;;
    *)          PLATFORM="UNKNOWN:${OS}"
esac

echo -e "${BLUE}Sistema detectado: ${PLATFORM}${NC}"
echo ""

# Verificar dependencias
echo "🔍 Verificando dependencias..."

# Función para verificar comando
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

MISSING_DEPS=()

if ! command_exists php; then
    MISSING_DEPS+=("PHP 8.3+")
fi

if ! command_exists composer; then
    MISSING_DEPS+=("Composer")
fi

if ! command_exists node; then
    MISSING_DEPS+=("Node.js 20+")
fi

if ! command_exists npm; then
    MISSING_DEPS+=("npm")
fi

if [ ${#MISSING_DEPS[@]} -ne 0 ]; then
    echo -e "${RED}❌ Faltan dependencias:${NC}"
    for dep in "${MISSING_DEPS[@]}"; do
        echo "   - $dep"
    done
    echo ""
    echo "Por favor instala las dependencias faltantes:"
    echo ""
    echo "📦 PHP 8.3+ y Composer:"
    echo "   Ubuntu/Debian: sudo apt install php8.3 php8.3-{pgsql,sqlite,zip,mbstring,xml,curl} composer"
    echo "   Mac: brew install php@8.3 composer"
    echo "   Windows: https://windows.php.net/download + https://getcomposer.org"
    echo ""
    echo "📦 Node.js 20+:"
    echo "   https://nodejs.org (descarga el instalador LTS)"
    echo ""
    exit 1
fi

echo -e "${GREEN}✅ Todas las dependencias están instaladas${NC}"
echo ""

# Obtener directorio del script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# ============================================
# BACKEND SETUP
# ============================================
echo -e "${BLUE}📦 Configurando Backend (Laravel)...${NC}"
echo ""

cd backend

# Instalar dependencias de Composer
echo "⬇️  Instalando dependencias de PHP..."
if [ ! -d "vendor" ]; then
    composer install --no-interaction --quiet
    echo -e "${GREEN}✅ Dependencias instaladas${NC}"
else
    echo -e "${YELLOW}⚠️  vendor/ ya existe, saltando...${NC}"
fi

# Configurar archivo .env
echo "⚙️  Configurando entorno..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    
    # Generar APP_KEY
    php artisan key:generate --quiet
    
    # Configurar SQLite para pruebas rápidas
    mkdir -p database
    touch database/database.sqlite
    
    # Actualizar .env para usar SQLite
    sed -i.bak 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
    sed -i.bak 's|DB_DATABASE=.*|DB_DATABASE='"$SCRIPT_DIR"'/backend/database/database.sqlite|' .env
    rm -f .env.bak
    
    echo -e "${GREEN}✅ Entorno configurado (SQLite)${NC}"
else
    echo -e "${YELLOW}⚠️  .env ya existe, saltando...${NC}"
fi

# Ejecutar migraciones
echo "🗄️  Creando base de datos..."
php artisan migrate:fresh --seed --force --quiet
echo -e "${GREEN}✅ Base de datos lista${NC}"

# Crear storage link
php artisan storage:link --quiet 2>/dev/null || true

cd ..

# ============================================
# FRONTEND SETUP
# ============================================
echo ""
echo -e "${BLUE}🎨 Configurando Frontend (Next.js)...${NC}"
echo ""

cd frontend

# Instalar dependencias de npm
echo "⬇️  Instalando dependencias de Node..."
if [ ! -d "node_modules" ]; then
    npm install --silent
    echo -e "${GREEN}✅ Dependencias instaladas${NC}"
else
    echo -e "${YELLOW}⚠️  node_modules/ ya existe, saltando...${NC}"
fi

# Configurar API URL
echo "⚙️  Configurando API..."
echo "NEXT_PUBLIC_API_URL=http://localhost:8000/api" > .env.local
echo -e "${GREEN}✅ API configurada${NC}"

cd ..

# ============================================
# INICIAR SERVIDORES
# ============================================
echo ""
echo -e "${GREEN}🚀 ¡Setup completado! Iniciando servidores...${NC}"
echo ""

# Función para limpiar procesos al salir
cleanup() {
    echo ""
    echo -e "${YELLOW}🛑 Deteniendo servidores...${NC}"
    if [ -n "$BACKEND_PID" ]; then
        kill $BACKEND_PID 2>/dev/null || true
    fi
    if [ -n "$FRONTEND_PID" ]; then
        kill $FRONTEND_PID 2>/dev/null || true
    fi
    echo -e "${GREEN}👋 Hasta luego!${NC}"
    exit 0
}

trap cleanup SIGINT SIGTERM

# Iniciar backend
echo -e "${BLUE}▶️  Iniciando Backend en http://localhost:8000${NC}"
cd backend
php artisan serve --host=0.0.0.0 --port=8000 > /tmp/backend.log 2>&1 &
BACKEND_PID=$!
cd ..

# Esperar a que backend esté listo
echo "⏳ Esperando backend..."
for i in {1..30}; do
    if curl -s http://localhost:8000 > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Backend listo!${NC}"
        break
    fi
    sleep 1
    if [ $i -eq 30 ]; then
        echo -e "${RED}❌ Timeout esperando backend${NC}"
        echo "Revisa el log: /tmp/backend.log"
        exit 1
    fi
done

# Iniciar frontend
echo -e "${BLUE}▶️  Iniciando Frontend en http://localhost:3000${NC}"
cd frontend
npm run dev > /tmp/frontend.log 2>&1 &
FRONTEND_PID=$!
cd ..

# Esperar a que frontend esté listo
echo "⏳ Esperando frontend..."
for i in {1..60}; do
    if curl -s http://localhost:3000 > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Frontend listo!${NC}"
        break
    fi
    sleep 1
    if [ $i -eq 60 ]; then
        echo -e "${RED}❌ Timeout esperando frontend${NC}"
        echo "Revisa el log: /tmp/frontend.log"
        exit 1
    fi
done

# ============================================
# RESUMEN
# ============================================
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║           🎉 EdTech Platform está corriendo!               ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║                                                            ║"
echo "║  🌐 Frontend:     http://localhost:3000                    ║"
echo "║  🔧 Backend API:  http://localhost:8000                    ║"
echo "║                                                            ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║                     PANTALLAS DISPONIBLES                  ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║                                                            ║"
echo "║  👤 Usuarios:                                              ║"
echo "║     • Login:      http://localhost:3000/login              ║"
echo "║     • Registro:   http://localhost:3000/register           ║"
echo "║                                                            ║"
echo "║  👑 Admin:                                                 ║"
echo "║     • Login:      http://localhost:3000/admin/login        ║"
echo "║     • Email:      admin@edtech.com                         ║"
echo "║     • Password:   admin123                                 ║"
echo "║                                                            ║"
echo "║  👨‍🏫 Instructor:                                            ║"
echo "║     • Dashboard:  http://localhost:3000/instructor/dashboard║"
echo "║     • Upload:     http://localhost:3000/instructor/upload  ║"
echo "║                                                            ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║  📊 Logs:                                                  ║"
echo "║     • Backend:    tail -f /tmp/backend.log                 ║"
echo "║     • Frontend:   tail -f /tmp/frontend.log                ║"
echo "║                                                            ║"
echo "║  🛑 Presiona Ctrl+C para detener los servidores            ║"
echo "║                                                            ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Abrir navegador (si es posible)
if command_exists xdg-open; then
    xdg-open http://localhost:3000 > /dev/null 2>&1 || true
elif command_exists open; then
    open http://localhost:3000 > /dev/null 2>&1 || true
fi

# Mantener script corriendo
echo -e "${YELLOW}💡 Los servidores están corriendo en segundo plano${NC}"
echo -e "${YELLOW}   Presiona Ctrl+C para detenerlos${NC}"
echo ""

wait
