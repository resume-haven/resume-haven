---
name: resumehaven-container-commands
description: Ensures all PHP, Composer, Artisan, npm, Pest, PHPStan, and Pint commands are always executed inside the correct ResumeHaven Docker container, never on the local host.
---

# ResumeHaven Container Commands

Use this skill to translate any local command into the correct `docker exec` form for the ResumeHaven setup.

## Trigger Names

- Primary Trigger: `container_commands`
- Alias: `resumehaven-container-commands`
- Source of truth for team trigger names: `.github/skills/copilot-skills.yaml`

## When to Use This Skill

Activate automatically whenever a command is suggested or requested that involves:

- `php`, `php artisan`
- `composer`
- `npm`, `npx`, `node`
- `vendor/bin/pest`, `vendor/bin/phpstan`, `vendor/bin/pint`
- Database migrations or seeds
- Cache clearing
- Any shell command that requires the Laravel runtime environment

## When Not to Use This Skill

- Pure `docker compose` lifecycle commands (`up`, `down`, `build`) — these run on the host
- `make` commands — these are already wrappers around `docker exec`
- Git commands — these run on the host
- IDE or editor commands

## Container Mapping

| Task | Container | Service |
|---|---|---|
| PHP, Artisan, Composer, Pest, PHPStan, Pint | `resumehaven-php` | PHP-FPM |
| npm, npx, Node scripts | `resumehaven-node` | Node LTS |
| Shell inspection (Nginx config) | `resumehaven-nginx` | Nginx |

## Command Reference

### PHP / Artisan

```bash
# Lokaler Befehl → immer so ausführen:
docker exec resumehaven-php php artisan migrate
docker exec resumehaven-php php artisan migrate:fresh --seed
docker exec resumehaven-php php artisan cache:clear
docker exec resumehaven-php php artisan config:clear
docker exec resumehaven-php php artisan test --compact tests/Unit/FooTest.php
```

### Composer

```bash
docker exec resumehaven-php composer install
docker exec resumehaven-php composer require vendor/package
docker exec resumehaven-php composer run setup
```

### Tests & Quality Gates

```bash
# Alle Tests
docker exec resumehaven-php composer run test:pest-all

# Gezielt (schneller)
docker exec resumehaven-php php artisan test --compact tests/Unit/FooTest.php

# Coverage (benötigt Xdebug → make debug-on)
docker exec resumehaven-php composer run test:pest-coverage

# PHPStan Level 9
docker exec resumehaven-php composer run phpstan

# Pint (nur geänderte Dateien)
docker exec resumehaven-php sh -c "cd /var/www/html && vendor/bin/pint --dirty --format agent"
```

### npm / Node

```bash
docker exec resumehaven-node npm install
docker exec resumehaven-node npm run build
docker exec resumehaven-node npm run dev
```

### Shell-Zugriff

```bash
docker exec -it resumehaven-php bash    # PHP-Container
docker exec -it resumehaven-node sh     # Node-Container
docker exec -it resumehaven-nginx sh    # Nginx-Container
```

## Make-Shortcuts (bevorzugt)

Wo ein `make`-Ziel existiert, dieses bevorzugen — es kapselt bereits den korrekten `docker exec`-Aufruf:

```bash
make test              # docker exec resumehaven-php composer run test:pest-all
make test-unit         # nur Unit-Tests
make test-coverage     # mit Xdebug
make phpstan           # PHPStan Level 9
make pint-fix          # Pint formatieren
make db-migrate        # php artisan migrate
make npm-build         # npm run build im Node-Container
make php-shell         # interaktive Shell im PHP-Container
```

## Guardrails

- Niemals `php` oder `npm` direkt auf dem Host ausführen — kein PHP und kein Node lokal installiert
- Niemals `composer` auf dem Host ausführen — Abhängigkeiten müssen im Container-Kontext aufgelöst werden
- Bei interaktiven Befehlen `-it` Flag ergänzen
- Bei Skript-/CI-Nutzung ohne TTY `-it` weglassen (nur `docker exec resumehaven-php ...`)

## Example Prompts

- "Übersetze diesen Befehl für ResumeHaven in den Container: `php artisan make:class Foo`"
- "Welchen `docker exec`-Befehl muss ich für `npm run build` im ResumeHaven-Setup verwenden?"
- "Nutze `container_commands` und führe die Migration und danach die Tests aus."

