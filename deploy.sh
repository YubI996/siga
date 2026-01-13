#!/bin/bash
set -e

# SIGA Production Deployment Script
# Usage: ./deploy.sh [build|start|stop|restart|logs|status|migrate|shell]

COMPOSE_FILE="docker-compose.prod.yml"
PROJECT_NAME="siga"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if .env file exists
check_env() {
    if [ ! -f .env ]; then
        log_error ".env file not found!"
        log_info "Copy .env.production to .env and configure it:"
        log_info "  cp .env.production .env"
        exit 1
    fi
}

# Build Docker images
build() {
    log_info "Building Docker images..."
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME build --no-cache
    log_info "Build completed!"
}

# Start all services
start() {
    check_env
    log_info "Starting services..."
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME up -d
    log_info "Services started!"
    log_info "Waiting for services to be healthy..."
    sleep 10
    status
}

# Stop all services
stop() {
    log_info "Stopping services..."
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME down
    log_info "Services stopped!"
}

# Restart all services
restart() {
    log_info "Restarting services..."
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME restart
    log_info "Services restarted!"
}

# View logs
logs() {
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME logs -f ${2:-}
}

# Show status
status() {
    log_info "Service status:"
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME ps
}

# Run migrations
migrate() {
    log_info "Running database migrations..."
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME exec app php artisan migrate --force
    log_info "Migrations completed!"
}

# Access shell
shell() {
    log_info "Accessing app shell..."
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME exec app sh
}

# Clear and rebuild caches
optimize() {
    log_info "Optimizing application..."
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME exec app php artisan config:cache
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME exec app php artisan route:cache
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME exec app php artisan view:cache
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME exec app php artisan event:cache
    log_info "Optimization completed!"
}

# Full deployment (build + start + migrate + optimize)
deploy() {
    check_env
    log_info "Starting full deployment..."

    log_info "Step 1/4: Building images..."
    build

    log_info "Step 2/4: Starting services..."
    start

    log_info "Step 3/4: Running migrations..."
    sleep 15  # Wait for MySQL to be fully ready
    migrate

    log_info "Step 4/4: Optimizing application..."
    optimize

    log_info "Deployment completed successfully!"
    status
}

# Backup database
backup() {
    BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
    log_info "Creating database backup: $BACKUP_FILE"
    docker compose -f $COMPOSE_FILE -p $PROJECT_NAME exec mysql mysqldump -u root -p${DB_ROOT_PASSWORD:-secret} ${DB_DATABASE:-siga} > $BACKUP_FILE
    log_info "Backup created: $BACKUP_FILE"
}

# Show help
help() {
    echo "SIGA Production Deployment Script"
    echo ""
    echo "Usage: ./deploy.sh [command]"
    echo ""
    echo "Commands:"
    echo "  build     Build Docker images"
    echo "  start     Start all services"
    echo "  stop      Stop all services"
    echo "  restart   Restart all services"
    echo "  logs      View logs (optionally specify service: logs app)"
    echo "  status    Show service status"
    echo "  migrate   Run database migrations"
    echo "  shell     Access app container shell"
    echo "  optimize  Clear and rebuild caches"
    echo "  deploy    Full deployment (build + start + migrate + optimize)"
    echo "  backup    Backup database"
    echo "  help      Show this help message"
}

# Main
case "${1:-help}" in
    build)
        build
        ;;
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    logs)
        logs "$@"
        ;;
    status)
        status
        ;;
    migrate)
        migrate
        ;;
    shell)
        shell
        ;;
    optimize)
        optimize
        ;;
    deploy)
        deploy
        ;;
    backup)
        backup
        ;;
    help|*)
        help
        ;;
esac
