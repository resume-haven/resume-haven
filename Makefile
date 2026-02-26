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

# --- LINT / FORMAT ---
pint-analyse: ## Pint: Nur Analyse (kein Fix)
	docker exec -it resumehaven-php composer run pint:analyse

pint-fix: ## Pint: Automatische Korrektur
	docker exec -it resumehaven-php composer run pint:fix

# --- DOCKER ---
docker-up: ## Docker-Container bauen und starten
	docker compose up -d --build

docker-down: ## Docker-Container stoppen
	docker compose down

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

php-cache-clear: ## Laravel Cache leeren (php artisan cache:clear)
	docker exec -it resumehaven-php php artisan cache:clear

.PHONY: help setup dev test test-unit test-feature test-acceptance pint-analyse pint-fix docker-up docker-down docker-logs docker-pint docker-test npm-build npm-dev php-shell node-shell nginx-shell php-cache-clear
