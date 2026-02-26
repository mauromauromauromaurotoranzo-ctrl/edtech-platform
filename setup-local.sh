#!/bin/bash

# ============================================
# EdTech Platform - Setup Local
# ============================================
# Script para desplegar y probar localmente

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funciones
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar dependencias
check_dependencies() {
    print_status "Verificando dependencias..."
    
    command -v docker >/dev/null 2>&1 || { print_error "Docker no está instalado. Instálalo primero."; exit 1; }
    command -v docker-compose >/dev/null 2>&1 || { print_error "Docker Compose no está instalado. Instálalo primero."; exit 1; }
    command -v npm >/dev/null 2>&1 || { print_error "Node.js/npm no está instalado. Instálalo primero."; exit 1; }
    
    print_success "Todas las dependencias están instaladas"
}

# Setup Backend
setup_backend() {
    print_status "Configurando Backend..."
    
    cd backend
    
    # Crear .env si no existe
    if [ ! -f .env ]; then
        print_status "Creando archivo .env..."
        cat > .env << 'EOF'
APP_NAME="EdTech Platform"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Shanghai

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=edtech
DB_USERNAME=edtech
DB_PASSWORD=secret

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@edtech.com"
MAIL_FROM_NAME="${APP_NAME}"

# APIs (opcional - para funcionalidad completa)
OPENROUTER_API_KEY=
ELEVENLABS_API_KEY=
OPENAI_API_KEY=
TELEGRAM_BOT_TOKEN=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
EOF
        print_success "Archivo .env creado"
    fi
    
    # Instalar dependencias si vendor no existe
    if [ ! -d vendor ]; then
        print_status "Instalando dependencias PHP..."
        docker run --rm -v $(pwd):/app -w /app composer:latest composer install --no-dev --optimize-autoloader
    fi
    
    # Generar key
    if [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
        print_status "Generando APP_KEY..."
        docker run --rm -v $(pwd):/app -w /app php:8.3-cli php artisan key:generate
    fi
    
    cd ..
    print_success "Backend configurado"
}

# Setup Frontend
setup_frontend() {
    print_status "Configurando Frontend..."
    
    cd frontend
    
    # Crear .env.local si no existe
    if [ ! -f .env.local ]; then
        print_status "Creando .env.local..."
        cat > .env.local << 'EOF'
NEXT_PUBLIC_API_URL=http://localhost:8000/api
NEXT_PUBLIC_APP_NAME="EdTech Platform"
EOF
        print_success "Archivo .env.local creado"
    fi
    
    # Instalar dependencias si node_modules no existe
    if [ ! -d node_modules ]; then
        print_status "Instalando dependencias Node.js..."
        npm install
    fi
    
    cd ..
    print_success "Frontend configurado"
}

# Iniciar con Docker
start_docker() {
    print_status "Iniciando servicios con Docker..."
    
    # Verificar si docker-compose.yml existe
    if [ ! -f docker-compose.yml ]; then
        print_error "No se encontró docker-compose.yml"
        exit 1
    fi
    
    # Construir e iniciar
    docker-compose up -d --build
    
    print_success "Servicios iniciados"
    
    # Esperar a que postgres esté listo
    print_status "Esperando a que PostgreSQL esté listo..."
    sleep 5
    
    # Ejecutar migraciones
    print_status "Ejecutando migraciones..."
    docker-compose exec -T app php artisan migrate --force || true
    
    # Seed básico (opcional)
    print_status "Creando usuario admin de prueba..."
    docker-compose exec -T app php artisan tinker --execute="
    use App\Infrastructure\Persistence\Eloquent\UserEloquentModel;
    use Illuminate\Support\Facades\Hash;
    
    UserEloquentModel::firstOrCreate(
        ['email' => 'admin@edtech.com'],
        [
            'name' => 'Admin',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]
    );
    echo 'Usuario admin creado: admin@edtech.com / admin123';
    " || true
}

# Iniciar frontend en modo desarrollo
start_frontend() {
    print_status "Iniciando frontend en modo desarrollo..."
    
    cd frontend
    
    # Iniciar en background
    npm run dev &
    FRONTEND_PID=$!
    
    cd ..
    
    print_success "Frontend iniciado (PID: $FRONTEND_PID)"
}

# Mostrar información final
show_info() {
    echo ""
    echo "=========================================="
    echo -e "${GREEN}✅ EdTech Platform está listo!${NC}"
    echo "=========================================="
    echo ""
    echo "🌐 URLs disponibles:"
    echo "   • Frontend:     http://localhost:3000"
    echo "   • Backend API:  http://localhost:8000/api"
    echo "   • Adminer (DB): http://localhost:8080"
    echo ""
    echo "👤 Credenciales de prueba:"
    echo "   • Email:    admin@edtech.com"
    echo "   • Password: admin123"
    echo ""
    echo "📁 Rutas del proyecto:"
    echo "   • Admin Login:  http://localhost:3000/admin/login"
    echo "   • Instructor:   http://localhost:3000/instructor/dashboard"
    echo "   • Upload:       http://localhost:3000/instructor/upload"
    echo ""
    echo "🐳 Comandos Docker útiles:"
    echo "   • Ver logs:     docker-compose logs -f"
    echo "   • Detener:      docker-compose down"
    echo "   • Reiniciar:    docker-compose restart"
    echo ""
    echo "⚠️  NOTA: Las APIs de IA requieren keys en backend/.env"
    echo "   (OpenRouter, ElevenLabs, OpenAI)"
    echo ""
    echo "Presiona Ctrl+C para detener el frontend"
    echo "=========================================="
}

# Menu interactivo
show_menu() {
    echo ""
    echo "=========================================="
    echo "    EdTech Platform - Setup Local"
    echo "=========================================="
    echo ""
    echo "Opciones:"
    echo "  1) Setup completo (Docker + Frontend)"
    echo "  2) Solo Docker (backend + DB)"
    echo "  3) Solo Frontend"
    echo "  4) Detener todo"
    echo "  5) Ver logs"
    echo "  q) Salir"
    echo ""
}

# Main
main() {
    # Si se pasa argumento, ejecutar directo
    if [ "$1" = "full" ]; then
        check_dependencies
        setup_backend
        setup_frontend
        start_docker
        start_frontend
        show_info
        # Mantener script corriendo
        wait
        exit 0
    fi
    
    if [ "$1" = "docker" ]; then
        check_dependencies
        setup_backend
        start_docker
        echo ""
        echo -e "${GREEN}✅ Backend corriendo en http://localhost:8000${NC}"
        exit 0
    fi
    
    if [ "$1" = "stop" ]; then
        print_status "Deteniendo servicios..."
        docker-compose down 2>/dev/null || true
        pkill -f "next dev" 2>/dev/null || true
        print_success "Servicios detenidos"
        exit 0
    fi
    
    # Menu interactivo
    while true; do
        show_menu
        read -p "Selecciona una opción: " choice
        
        case $choice in
            1)
                check_dependencies
                setup_backend
                setup_frontend
                start_docker
                start_frontend
                show_info
                wait
                ;;
            2)
                check_dependencies
                setup_backend
                start_docker
                echo ""
                echo -e "${GREEN}✅ Backend listo en http://localhost:8000${NC}"
                ;;
            3)
                setup_frontend
                cd frontend && npm run dev
                ;;
            4)
                print_status "Deteniendo servicios..."
                docker-compose down 2>/dev/null || true
                pkill -f "next dev" 2>/dev/null || true
                print_success "Servicios detenidos"
                ;;
            5)
                docker-compose logs -f
                ;;
            q|Q)
                echo "Saliendo..."
                exit 0
                ;;
            *)
                print_error "Opción inválida"
                ;;
        esac
    done
}

# Ejecutar
main "$@"
