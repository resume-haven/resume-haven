#!/usr/bin/env bash
set -e

DOMAIN="resume-haven.localhost"
CERT_DIR="./traefik/certs"

echo "🔧 Starte mkcert Setup unter WSL"

# 1. mkcert installieren, falls nicht vorhanden
if ! command -v mkcert >/dev/null 2>&1; then
  echo "📦 mkcert nicht gefunden – Installation wird durchgeführt..."
  sudo apt update
  sudo apt install -y libnss3-tools wget

  wget https://github.com/FiloSottile/mkcert/releases/latest/download/mkcert-linux-amd64
  chmod +x mkcert-linux-amd64
  sudo mv mkcert-linux-amd64 /usr/local/bin/mkcert

  echo "✅ mkcert installiert"
else
  echo "✅ mkcert bereits installiert"
fi

# 2. CA installieren
echo "🔐 Installiere lokale CA (falls nicht vorhanden)"
mkcert -install

# 3. Zertifikate erzeugen
echo "📄 Erzeuge Zertifikate für $DOMAIN"
mkcert "$DOMAIN"

# 4. Zielordner anlegen
mkdir -p "$CERT_DIR"

# 5. Zertifikate verschieben
echo "📁 Kopiere Zertifikate nach $CERT_DIR"
mv "${DOMAIN}.pem" "$CERT_DIR/${DOMAIN}.pem"
mv "${DOMAIN}-key.pem" "$CERT_DIR/${DOMAIN}-key.pem"

echo "🎉 mkcert Setup unter WSL abgeschlossen"
