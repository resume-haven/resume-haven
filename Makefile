.PHONY: help up down build logs shell install update test lint clean prune restart healthcheck

# ============================================================================
# Makefile für ResumeHaven
# ============================================================================
#
# Dieses Makefile automatisiert häufige Entwicklungsaufgaben.
# Die Help-Seite wird automatisch aus den Kommentaren (## GRUPPE: target - Beschreibung)
# generiert, die direkt über jedem Target definiert sind.
#
# Format der Help-Kommentare:
#   ## GRUPPE: target - Beschreibung
#   target:
#       @echo "Doing something"
#
# Die 'help' Regel parsed diese Kommentare mit awk/grep und formatiert die Ausgabe.
# Vorteile:
# - DRY Prinzip: Nur eine Quelle für Dokumentation
# - Automatisch synchron mit Targets
# - Gruppierung und Sortierung automatisch
# - Leicht zu erweitern: Neues Target + Kommentar
#
# ============================================================================

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
NC := \033[0m # No Color

# Default target
help:
	@echo "$(BLUE)ResumeHaven - Available Commands$(NC)"
	@echo ""
	@awk '/^## /{match($$0, /^## ([^:]+): ([^ ]+) - (.+)/, arr); group=arr[1]; cmd=arr[2]; desc=arr[3]; if(group != prev_group && prev_group != "") print ""; if(group != prev_group) {printf "$(GREEN)%s$(NC)\n", group; prev_group=group} printf "  make %-15s %s\n", cmd, desc}' $(MAKEFILE_LIST)
	@echo ""

# ============================================================================
# DOCKER MANAGEMENT
# ============================================================================

## Docker Management: up - Start Docker containers in background
up:
	@echo "$(BLUE)Starting Docker containers...$(NC)"
	docker-compose up -d
	@sleep 2
	@make healthcheck

## Docker Management: down - Stop and remove containers
down:
	@echo "$(BLUE)Stopping Docker containers...$(NC)"
	docker-compose down

## Docker Management: build - Build Docker image
build:
	@echo "$(BLUE)Building Docker image...$(NC)"
	docker-compose build app

## Docker Management: rebuild - Rebuild Docker image (no cache)
rebuild:
	@echo "$(BLUE)Rebuilding Docker image (no cache)...$(NC)"
	docker-compose build --no-cache app

## Docker Management: restart - Restart containers
restart:
	@echo "$(BLUE)Restarting containers...$(NC)"
	docker-compose restart app
	@sleep 2
	@make healthcheck

## Docker Management: logs - View container logs (follow mode)
logs:
	@echo "$(BLUE)Following container logs (Ctrl+C to exit)...$(NC)"
	docker-compose logs -f app

## Docker Management: shell - Open shell in app container
shell:
	@echo "$(BLUE)Opening shell in app container...$(NC)"
	docker-compose exec app bash

## Docker Management: healthcheck - Check container health status
healthcheck:
	@echo "$(BLUE)Checking container health...$(NC)"
	@docker-compose ps

# ============================================================================
# APPLICATION
# ============================================================================

## Application: install - Install Composer dependencies
install:
	@echo "$(BLUE)Installing Composer dependencies...$(NC)"
	docker-compose exec app composer install

## Application: update - Update Composer dependencies
update:
	@echo "$(BLUE)Updating Composer dependencies...$(NC)"
	docker-compose exec app composer update

## Application: test - Run PHP tests
test:
	@echo "$(BLUE)Running tests...$(NC)"
	docker-compose exec app php artisan test

## Application: lint - Run code style checks
lint:
	@echo "$(BLUE)Running code linting...$(NC)"
	docker-compose exec app composer lint

## Application: migrate - Run database migrations
migrate:
	@echo "$(BLUE)Running database migrations...$(NC)"
	docker-compose exec app php artisan migrate

## Application: php-version - Show PHP version and extensions
php-version:
	@echo "$(BLUE)PHP Version:$(NC)"
	docker-compose exec app php -v

# ============================================================================
# MAINTENANCE
# ============================================================================

## Maintenance: clean - Remove containers and networks
clean:
	@echo "$(YELLOW)Removing containers and networks...$(NC)"
	docker-compose down
	@echo "$(GREEN)Cleaned!$(NC)"

## Maintenance: prune - Clean up Docker system (images, volumes)
prune:
	@echo "$(YELLOW)Pruning Docker system...$(NC)"
	docker system prune -f
	@echo "$(GREEN)Pruned!$(NC)"

## Maintenance: fix-perms - Fix file permissions in volumes
fix-perms:
	@echo "$(YELLOW)Fixing file permissions...$(NC)"
	chmod -R 755 storage bootstrap/cache
	docker-compose exec app chown -R www-data:www-data /var/www/html
	@echo "$(GREEN)Permissions fixed!$(NC)"

# ============================================================================
# DEVELOPMENT
# ============================================================================

## Development: dev - Complete dev setup (up + install)
dev: up install
	@echo "$(GREEN)Development environment ready!$(NC)"
	@echo "Access the app at: http://localhost"

## Development: debug - Start debug listener and show instructions
debug:
	@echo "$(BLUE)Starting debug listener...$(NC)"
	@echo "Press Ctrl+C to stop listening"
	@echo "1. Set breakpoint in VS Code"
	@echo "2. Open http://localhost in browser"
	@echo "3. VS Code will pause at breakpoint"

## Development: init - Initialize project (clean build + up + install)
init: down build up install
	@echo "$(GREEN)Project initialized and ready!$(NC)"

## Development: reset - Reset project to fresh state
reset: clean build up install
	@echo "$(GREEN)Project reset complete!$(NC)"

## Development: status - Show project status
status:
	@echo "$(BLUE)=== Project Status ===$(NC)"
	@echo ""
	@echo "$(BLUE)Containers:$(NC)"
	@docker-compose ps
	@echo ""
	@echo "$(BLUE)PHP Info:$(NC)"
	@docker-compose exec app php -v | head -n 3
	@echo ""
	@echo "$(BLUE)Extensions:$(NC)"
	@docker-compose exec app php -m | grep -E "xdebug|pdo|zip|gd"
	@echo ""

## Development: laravel-install - Install Laravel framework (preserves Docker config)
laravel-install: up
	@echo "$(BLUE)Installing Laravel...$(NC)"
	@echo "$(YELLOW)This will install Laravel while preserving Docker and docs$(NC)"
	@echo ""
	@docker-compose exec app sh -c " \
		cd /var/www/html && \
		echo 'Creating Laravel project in temp directory...' && \
		composer create-project --prefer-dist laravel/laravel /tmp/laravel-temp && \
		echo '' && \
		echo 'Moving Laravel files to project root...' && \
		cp -r /tmp/laravel-temp/* . && \
		cp /tmp/laravel-temp/.gitignore . 2>/dev/null || true && \
		cp /tmp/laravel-temp/.env.example . 2>/dev/null || true && \
		rm -rf /tmp/laravel-temp && \
		echo '' && \
		echo 'Configuring SQLite database...' && \
		touch storage/database.sqlite && \
		sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env && \
		sed -i 's|DB_DATABASE=.*|DB_DATABASE=/var/www/html/storage/database.sqlite|' .env && \
		sed -i '/^DB_HOST=/d' .env && \
		sed -i '/^DB_PORT=/d' .env && \
		sed -i '/^DB_USERNAME=/d' .env && \
		sed -i '/^DB_PASSWORD=/d' .env && \
		echo '' && \
		echo 'Generating application key...' && \
		php artisan key:generate && \
		echo '' && \
		echo 'Setting permissions...' && \
		chmod -R 775 storage bootstrap/cache && \
		chown -R www-data:www-data storage bootstrap/cache && \
		echo '' && \
		echo 'Laravel installed successfully!' && \
		echo '' && \
		echo 'Next steps:' && \
		echo '  1. Run: make laravel-strict-types' && \
		echo '  2. Run: make migrate' && \
		echo '  3. Access: http://localhost' \
	"
	@echo ""
	@echo "$(GREEN)Laravel installation complete!$(NC)"

## Development: laravel-strict-types - Add strict_types to all PHP files
laravel-strict-types:
	@echo "$(BLUE)Adding strict_types to all PHP files...$(NC)"
	@docker-compose exec app php scripts/add-strict-types.php
	@echo "$(GREEN)Done!$(NC)"

.DEFAULT_GOAL := help

.PHONY: up down build rebuild restart logs shell healthcheck
.PHONY: install update test lint migrate php-version
.PHONY: clean prune fix-perms
.PHONY: dev debug init reset status laravel-install laravel-strict-types
