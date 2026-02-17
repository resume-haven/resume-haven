UID := $(shell id -u)
GID := $(shell id -g)

export UID
export GID

# -------------------------
# Basis-Kommandos
# -------------------------

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose down
	docker compose up -d

logs:
	docker compose logs -f

ps:
	docker compose ps

# -------------------------
# Pimcore Installation / Maintenance
# -------------------------

install:
	docker compose exec php vendor/bin/pimcore-install --no-interaction

install-force:
	docker compose exec php vendor/bin/pimcore-install --no-interaction --ignore-existing-config

reset-db:
	docker compose exec db mysql -u root -p$${MYSQL_ROOT_PASSWORD} -e "DROP DATABASE IF EXISTS $${MYSQL_DATABASE}; CREATE DATABASE $${MYSQL_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;"

# -------------------------
# Tests
# -------------------------

test-prepare:
	docker compose run --user=root --rm test-php chown -R $(UID):$(GID) var/ public/var/
	docker compose run --rm test-php vendor/bin/pimcore-install -n

test:
	docker compose run --rm test-php vendor/bin/codecept run -vv

# -------------------------
# Bootstrap (siehe bootstrap.sh)
# -------------------------

bootstrap:
	./bootstrap.sh

# -------------------------
# Traefik
# -------------------------

traefik-reload:
	docker compose exec traefik traefik reload

traefik-logs:
	docker compose logs -f traefik

# -------------------------
# Monitoring Stack
# -------------------------

monitoring-up:
	docker compose -f docker-compose.yml -f docker-compose.monitoring.yml up -d

monitoring-down:
	docker compose -f docker-compose.yml -f docker-compose.monitoring.yml down

monitoring-restart:
	docker compose -f docker-compose.yml -f docker-compose.monitoring.yml down
	docker compose -f docker-compose.yml -f docker-compose.monitoring.yml up -d

monitoring-logs:
	docker compose -f docker-compose.yml -f docker-compose.monitoring.yml logs -f

monitoring-grafana:
	@echo "Grafana läuft unter http://localhost:3001"

monitoring-prometheus:
	@echo "Prometheus läuft unter http://localhost:9090"

monitoring-loki:
	@echo "Loki API läuft unter http://localhost:3100"

monitoring-rabbitmq:
	@echo "RabbitMQ Dashboard verfügbar unter http://localhost:3001"
	@echo "Dashboard ID: 10991"
