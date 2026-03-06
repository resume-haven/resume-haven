# ResumeHaven Makefile (WSL)
#
# Übersicht: make <ziel>
#
# Gruppen:
#   setup      – Projekt initialisieren und Abhängigkeiten installieren
#   dev        – Entwicklung und lokaler Server
#   test       – Tests (alle, Unit, Feature, Acceptance)
#   lint       – Code-Analyse und Formatierung
#   docker     – Docker-Kommandos
#
# Hilfe anzeigen:
help:
	@grep -E '^[a-zA-Z_-]+:.*?##' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# --- SETUP ---
setup: ## Projekt initialisieren (Composer, NPM, .env, Migration, Build)
	docker exec -it resumehaven-php composer run setup

# --- DEV ---
dev: ## Lokalen Entwicklungsserver und Assets starten
	docker exec -it resumehaven-php composer run dev

# --- TESTS ---
test: ## Alle Tests ausführen (Pest)
	docker exec -it resumehaven-php composer run test:pest-all

test-unit: ## Nur Unit-Tests ausführen
	docker exec -it resumehaven-php composer run test:pest-unit

test-feature: ## Nur Feature-Tests ausführen
	docker exec -it resumehaven-php composer run test:pest-feature

test-acceptance: ## Nur Acceptance-Tests ausführen
	docker exec -it resumehaven-php vendor/bin/pest --group=acceptance

test-coverage: ## Testabdeckung mit Xdebug anzeigen
	docker exec -it resumehaven-php composer run test:pest-coverage

# --- LINT / FORMAT ---
pint-analyse: ## Pint: Nur Analyse (kein Fix)
	docker exec -it resumehaven-php composer run pint:analyse

pint-fix: ## Pint: Automatische Korrektur
	docker exec -it resumehaven-php composer run pint:fix

phpstan: ## PHPStan: Statische Code-Analyse
	docker exec -it resumehaven-php composer run phpstan

phpstan-baseline: ## PHPStan: Baseline generieren
	docker exec -it resumehaven-php composer run phpstan:baseline

# --- DOCKER ---
docker-up: ## Docker-Container bauen und starten
	docker compose up -d --build

docker-down: ## Docker-Container stoppen
	docker compose down

docker-restart: ## Docker-Container neu starten (schnell, ohne Rebuild)
	docker compose restart

docker-rebuild: ## Docker-Container komplett neu bauen (nach Config-Änderungen)
	docker compose down
	docker compose build --no-cache php
	docker compose up -d

docker-stop: ## Docker-Container stoppen (Alias für docker-down)
	docker compose stop

docker-start: ## Docker-Container starten
	docker compose start

docker-build: ## Docker-Container bauen
	docker compose build

docker-clean: ## Docker-Container und Volumes löschen
	docker compose down -v

docker-logs: ## Docker-Logs anzeigen
	docker compose logs -f

docker-pint: ## Pint im PHP-Container ausführen
	docker exec -it resumehaven-php vendor/bin/pint --config pint.json .

docker-test: ## Tests im PHP-Container ausführen
	docker exec -it resumehaven-php vendor/bin/pest

# --- NPM / NODE ---
npm-build: ## Assets bauen (npm run build im Node-Container)
	docker exec -it resumehaven-node npm run build

npm-dev: ## Assets im Watch-Modus (npm run dev im Node-Container)
	docker exec -it resumehaven-node npm run dev

# --- SHELLS ---
php-shell: ## PHP-Container Shell öffnen
	docker exec -it resumehaven-php bash

node-shell: ## Node-Container Shell öffnen
	docker exec -it resumehaven-node sh

nginx-shell: ## Nginx-Container Shell öffnen
	docker exec -it resumehaven-nginx sh

# --- DEBUG (Xdebug) ---
debug-on: ## 🐛 Xdebug aktivieren (mit override + rebuild)
	@echo "🐛 Xdebug wird aktiviert..."
	@if [ ! -f docker-compose.override.yml ]; then \
		echo "  → Erstelle docker-compose.override.yml..."; \
		cp docker-compose.override.example.yml docker-compose.override.yml || \
		(echo "services:" > docker-compose.override.yml && \
		 echo "  php:" >> docker-compose.override.yml && \
		 echo "    build:" >> docker-compose.override.yml && \
		 echo "      args:" >> docker-compose.override.yml && \
		 echo "        INSTALL_XDEBUG: 'true'" >> docker-compose.override.yml && \
		 echo "    environment:" >> docker-compose.override.yml && \
		 echo "      XDEBUG_MODE: debug,coverage" >> docker-compose.override.yml && \
		 echo "      XDEBUG_CONFIG: \"client_host=host.docker.internal client_port=9003 idekey=resumehaven\"" >> docker-compose.override.yml); \
	fi
	docker compose down
	docker compose build --no-cache php
	docker compose up -d
	@sleep 2
	@echo "✅ Xdebug aktiviert! Port 9003 bereit."
	@echo "   IDE auf Port 9003 lauschen lassen."
	@echo "   VSCode: F5 drücken"
	@echo "   PhpStorm: Run → Break on first line"

debug-off: ## 🚀 Xdebug deaktivieren (normal schnell)
	@echo "🚀 Xdebug wird deaktiviert..."
	@rm -f docker-compose.override.yml
	docker compose down
	docker compose build --no-cache php
	docker compose up -d
	@sleep 2
	@echo "✅ Xdebug deaktiviert! Schneller Mode aktiv."

debug-status: ## 📊 Xdebug-Status anzeigen
	@docker exec resumehaven-php php -v
	@docker exec resumehaven-php php -m | grep -i xdebug && echo "✅ Xdebug ist INSTALLIERT" || echo "❌ Xdebug ist NICHT installiert"
	@docker exec resumehaven-php php -r "echo 'XDEBUG_MODE: ' . getenv('XDEBUG_MODE') . PHP_EOL;" 2>/dev/null || echo "  (Env nicht gesetzt)"

debug-test: ## 🧪 Test-Request mit Xdebug-Cookie
	@echo "Sende Request mit XDEBUG_SESSION cookie..."
	@curl -s -b "XDEBUG_SESSION=resumehaven" http://localhost:8080 | head -20

debug-logs: ## 📋 Xdebug-Logs anzeigen
	docker compose logs -f resumehaven-php | grep -i xdebug || echo "Keine Xdebug-Logs"

# --- DATABASE ---
db-migrate: ## Datenbank-Migrationen ausführen
	docker exec -it resumehaven-php php artisan migrate

db-migrate-status: ## Status der Datenbank-Migrationen anzeigen
	docker exec -it resumehaven-php php artisan migrate:status

db-migrate-rollback: ## Letzte Datenbank-Migration rückgängig machen
	docker exec -it resumehaven-php php artisan migrate:rollback

db-migrate-refresh: ## Alle Migrationen zurücksetzen und neu ausführen
	docker exec -it resumehaven-php php artisan migrate:refresh

db-seed: ## Datenbank mit Seeds befüllen
	docker exec -it resumehaven-php php artisan db:seed

.PHONY: help setup dev test test-unit test-feature test-acceptance test-coverage pint-analyse pint-fix phpstan phpstan-baseline docker-up docker-down docker-restart docker-rebuild docker-stop docker-start docker-build docker-clean docker-logs docker-pint docker-test npm-build npm-dev php-shell node-shell nginx-shell debug-on debug-off debug-status debug-test debug-logs db-migrate db-migrate-status db-migrate-rollback db-migrate-refresh db-seed
