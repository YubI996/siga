# SIGA Docker Makefile
.PHONY: help build up down restart logs shell mysql redis npm artisan fresh test
.PHONY: prod-build prod-up prod-down prod-logs prod-deploy prod-migrate prod-shell

# Default target
help:
	@echo "SIGA Docker Commands"
	@echo ""
	@echo "=== Development ==="
	@echo "  make build      - Build dev Docker images"
	@echo "  make up         - Start dev containers"
	@echo "  make down       - Stop dev containers"
	@echo "  make restart    - Restart dev containers"
	@echo "  make logs       - View dev container logs"
	@echo "  make shell      - Access dev app container shell"
	@echo "  make mysql      - Access MySQL CLI"
	@echo "  make redis      - Access Redis CLI"
	@echo "  make npm        - Run npm commands (e.g., make npm cmd='run build')"
	@echo "  make artisan    - Run artisan commands (e.g., make artisan cmd='migrate')"
	@echo "  make fresh      - Fresh install (migrate:fresh --seed)"
	@echo "  make test       - Run tests"
	@echo "  make setup      - Initial dev project setup"
	@echo ""
	@echo "=== Production ==="
	@echo "  make prod-build   - Build production Docker images"
	@echo "  make prod-up      - Start production containers"
	@echo "  make prod-down    - Stop production containers"
	@echo "  make prod-logs    - View production logs"
	@echo "  make prod-deploy  - Full production deployment"
	@echo "  make prod-migrate - Run production migrations"
	@echo "  make prod-shell   - Access production app shell"
	@echo "  make prod-backup  - Backup production database"

# ========================
# Development Commands
# ========================

# Build Docker images
build:
	docker compose build

# Start containers
up:
	docker compose up -d

# Stop containers
down:
	docker compose down

# Restart containers
restart:
	docker compose restart

# View logs
logs:
	docker compose logs -f

# Access app shell
shell:
	docker compose exec app bash

# Access MySQL CLI
mysql:
	docker compose exec mysql mysql -u siga -psecret siga

# Access Redis CLI
redis:
	docker compose exec redis redis-cli

# Run npm commands
npm:
	docker compose run --rm node npm $(cmd)

# Run artisan commands
artisan:
	docker compose exec app php artisan $(cmd)

# Fresh migration with seed
fresh:
	docker compose exec app php artisan migrate:fresh --seed

# Run tests
test:
	docker compose exec app php artisan test

# Initial setup
setup:
	@echo "Building containers..."
	docker compose build
	@echo "Starting containers..."
	docker compose up -d
	@echo "Installing Composer dependencies..."
	docker compose exec app composer install
	@echo "Generating app key..."
	docker compose exec app php artisan key:generate
	@echo "Running migrations..."
	docker compose exec app php artisan migrate
	@echo "Installing npm dependencies..."
	docker compose run --rm node npm install
	@echo "Building frontend assets..."
	docker compose run --rm node npm run build
	@echo "Setup complete! Access the app at http://localhost:8080"

# ========================
# Production Commands
# ========================

PROD_COMPOSE = docker compose -f docker-compose.prod.yml -p siga

# Build production images
prod-build:
	$(PROD_COMPOSE) build --no-cache

# Start production containers
prod-up:
	$(PROD_COMPOSE) up -d

# Stop production containers
prod-down:
	$(PROD_COMPOSE) down

# View production logs
prod-logs:
	$(PROD_COMPOSE) logs -f

# Access production shell
prod-shell:
	$(PROD_COMPOSE) exec app sh

# Run production migrations
prod-migrate:
	$(PROD_COMPOSE) exec app php artisan migrate --force

# Optimize production
prod-optimize:
	$(PROD_COMPOSE) exec app php artisan config:cache
	$(PROD_COMPOSE) exec app php artisan route:cache
	$(PROD_COMPOSE) exec app php artisan view:cache
	$(PROD_COMPOSE) exec app php artisan event:cache

# Full production deployment
prod-deploy:
	@echo "Starting production deployment..."
	@echo "Step 1/4: Building images..."
	$(PROD_COMPOSE) build --no-cache
	@echo "Step 2/4: Starting services..."
	$(PROD_COMPOSE) up -d
	@echo "Step 3/4: Waiting for services..."
	sleep 15
	@echo "Step 4/4: Running migrations..."
	$(PROD_COMPOSE) exec app php artisan migrate --force
	@echo "Deployment completed!"
	$(PROD_COMPOSE) ps

# Backup production database
prod-backup:
	@echo "Creating database backup..."
	$(PROD_COMPOSE) exec mysql mysqldump -u root -p$${DB_ROOT_PASSWORD} $${DB_DATABASE} > backup_$$(date +%Y%m%d_%H%M%S).sql
	@echo "Backup created!"

# Production status
prod-status:
	$(PROD_COMPOSE) ps
