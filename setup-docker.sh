#!/bin/bash

# 🐳 Script de Setup con Docker para EdTech Platform

set -e

echo "🎓 EdTech Platform - Setup con Docker"
echo "======================================"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Verificar Docker
echo "🔍 Verificando Docker..."
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker no está instalado${NC}"
    echo "Instálalo desde: https://docs.docker.com/get-docker/"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose no está instalado${NC}"
    echo "Instálalo desde: https://docs.docker.com/compose/install/"
    exit 1
fi

echo -e "${GREEN}✅ Docker encontrado${NC}"
echo ""

# Directorio del proyecto
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Limpiar builds anteriores (opcional)
echo -e "${YELLOW}⚠️  ¿Quieres limpiar builds anteriores? (recomendado si hay errores) [s/N]${NC}"
read -t 5 -n 1 CLEAN_BUILD || CLEAN_BUILD="n"
echo ""

if [[ $CLEAN_BUILD =~ ^[Ss]$ ]]; then
    echo "🧹 Limpiando builds anteriores..."
    docker-compose down -v --remove-orphans 2>/dev/null || true
    docker system prune -f 2>/dev/null || true
    echo -e "${GREEN}✅ Limpieza completada${NC}"
    echo ""
fi

# Construir e iniciar servicios
echo -e "${BLUE}🏗️  Construyendo imágenes Docker...${NC}"
echo "(Este proceso puede tomar varios minutos la primera vez)"
echo ""

docker-compose build --no-cache

echo ""
echo -e "${BLUE}🚀 Iniciando servicios...${NC}"
docker-compose up -d

# Esperar a que la base de datos esté lista
echo ""
echo -e "${BLUE}⏳ Esperando base de datos...${NC}"
for i in {1..30}; do
    if docker-compose exec -T db pg_isready -U postgres > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Base de datos lista${NC}"
        break
    fi
    sleep 1
    if [ $i -eq 30 ]; then
        echo -e "${RED}❌ Timeout esperando base de datos${NC}"
        exit 1
    fi
done

# Ejecutar migraciones
echo ""
echo -e "${BLUE}🗄️  Ejecutando migraciones...${NC}"
docker-compose exec -T app php artisan migrate:fresh --seed --force

# Configurar storage
echo ""
echo -e "${BLUE}📁 Configurando storage...${NC}"
docker-compose exec -T app php artisan storage:link 2>/dev/null || true
docker-compose exec -T app chown -R www-data:www-data storage bootstrap/cache

# Mostrar estado
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║           🎉 EdTech Platform con Docker listo!             ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║                                                            ║"
echo "║  🌐 Frontend:     http://localhost:3000                    ║"
echo "║  🔧 Backend API:  http://localhost:8000                    ║"
echo "║  🗄️  PostgreSQL:  localhost:5432                          ║"
echo "║                                                            ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║                     COMANDOS ÚTILES                        ║"
echo "╠════════════════════════════════════════════════════════════╣"
echo "║                                                            ║"
echo "║  Ver logs:         docker-compose logs -f                  ║"
echo "║  Detener:          docker-compose down                     ║"
echo "║  Reiniciar:        docker-compose restart                  ║"
echo "║  Reconstruir:      docker-compose up -d --build            ║"
echo "║                                                            ║"
echo "║  Acceder al contenedor:                                    ║"
echo "║  docker-compose exec app bash                              ║"
echo "║                                                            ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Verificar frontend
if [ -d "frontend" ]; then
    echo -e "${YELLOW}⚠️  Para el frontend, ejecuta en otra terminal:${NC}"
    echo "   cd frontend && npm install && npm run dev"
    echo ""
fi

echo -e "${GREEN}✅ Todos los servicios están corriendo!${NC}"
