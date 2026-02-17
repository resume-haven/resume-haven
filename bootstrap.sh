#!/usr/bin/env bash
set -e

echo "🔧 Bootstrap resume-haven / Pimcore"

# 1. .env anlegen, falls nicht vorhanden
if [ ! -f .env ]; then
  echo "📄 Erzeuge .env aus .env.example"
  cp .env.example .env
  echo "⚠️ Bitte prüfe und passe .env bei Bedarf an."
else
  echo "✅ .env existiert bereits – überspringe Erstellung."
fi

# 2. Submodule initialisieren (falls noch nicht)
if [ -d .git ] && [ -f .gitmodules ]; then
  echo "🔁 Initialisiere Git Submodule"
  git submodule update --init --recursive
else
  echo "ℹ️ Keine .gitmodules gefunden – Submodule werden übersprungen."
fi

# 3. Docker-Stack starten
echo "🐳 Starte Docker-Services"
docker compose up -d

# 4. Pimcore-Installation prüfen/ausführen
echo "🔍 Prüfe, ob Pimcore bereits installiert ist..."

if docker compose exec php test -f var/config/system.yml >/dev/null 2>&1; then
  echo "✅ Pimcore scheint bereits installiert zu sein (var/config/system.yml gefunden)."
else
  echo "🚀 Installiere Pimcore (dies kann einige Minuten dauern)..."
  docker compose exec php vendor/bin/pimcore-install --no-interaction
  echo "✅ Pimcore-Installation abgeschlossen."
fi

echo "✅ Bootstrap abgeschlossen."
