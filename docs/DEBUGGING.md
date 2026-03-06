# 🐛 Debugging mit Xdebug

Vollständige Anleitung für lokales Debugging mit Xdebug in Docker.

---

## ⚡ Quick Start

```bash
make debug-on       # Xdebug aktivieren
make debug-status   # Status prüfen
make php-shell      # In Container gehen (XDEBUG_CONFIG ist bereits gesetzt!)
php artisan tinker  # Debuggen!
```

---

## 📋 Verfügbare Kommandos

| Kommando | Beschreibung |
|----------|-------------|
| `make debug-on` | Xdebug aktivieren (rebuild mit override.yml) |
| `make debug-off` | Xdebug deaktivieren (normaler schneller Mode) |
| `make debug-status` | Xdebug-Status prüfen |
| `make debug-test` | Test-Request mit XDEBUG_SESSION Cookie senden |
| `make debug-logs` | Xdebug-Logs anzeigen |

---

## 🔧 Wie es funktioniert

### **Architektur:**

1. **Build-Zeit (`make debug-on`):**
   - `docker-compose.override.yml` wird erstellt/kopiert
   - PHP-Container mit `INSTALL_XDEBUG=true` gebaut
   - Xdebug wird via `pecl install xdebug` kompiliert

2. **Runtime (nach `make debug-on`):**
   - `XDEBUG_CONFIG` Env-Variable ist gesetzt
   - Xdebug lauscht auf Port 9003
   - IDE verbindet sich zu Port 9003 (Server-Modus)

3. **Deaktivieren (`make debug-off`):**
   - `docker-compose.override.yml` wird gelöscht
   - Rebuild ohne Xdebug
   - Alles läuft 50% schneller

---

## 🎯 VSCode Setup

### **1. Erweiterung installieren**

- Install: "PHP Debug" (Felix Becker) oder "PHP Intelephense"

### **2. Launch-Konfiguration**

Die Datei `.vscode/launch.json` wird automatisch erstellt, enthalten sollte:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug (Docker)",
            "type": "php",
            "port": 9003,
            "pathMapping": {
                "/var/www/html": "${workspaceFolder}/src"
            },
            "log": false,
            "xdebugSettings": {
                "max_data": 65535,
                "show_hidden": 1,
                "max_children": 100
            }
        }
    ]
}
```

### **3. Debuggen**

1. **Xdebug aktivieren:**
   ```bash
   make debug-on
   ```

2. **Breakpoint setzen:** Zeile klicken (roten Punkt)

3. **Debugger starten:** VSCode → Run and Debug (Ctrl+Shift+D) → "Listen for Xdebug (Docker)"

4. **Script ausführen:**
   ```bash
   make php-shell
   php artisan tinker
   ```
   → Debugger stoppt bei Breakpoint!

5. **Debugging Controls:**
   - ▶ Continue (F5)
   - ⊙ Step Over (F10)
   - ↘ Step Into (F11)
   - ↖ Step Out (Shift+F11)

---

## 🎯 PhpStorm Setup

### **1. Server konfigurieren**

- `Preferences → Languages & Frameworks → PHP → Servers`
- **Neuer Server:**
  - Name: `localhost`
  - Host: `localhost`
  - Port: `8080`
  - Debugger: `Xdebug`
  - Path Mapping:
    - `/var/www/html` → `<project>/src`

### **2. Debug-Konfiguration**

- `Run → Edit Configurations`
- **Neuer PHP Remote Debug:**
  - Server: `localhost`
  - Port: `9003`

### **3. Debuggen**

1. **Xdebug aktivieren:**
   ```bash
   make debug-on
   ```

2. **Breakpoint setzen:** Zeile klicken

3. **Debugger starten:** 
   - `Run → Debug 'PHP Remote Debug'` (Shift+F9)
   - oder: `Run → Break on first line`

4. **Script ausführen:**
   ```bash
   make php-shell
   php artisan test
   ```
   → Debugger stoppt bei Breakpoint!

---

## 💻 CLI-Debugging (Mit und ohne IDE)

### **Variante A: Mit IDE**

```bash
make debug-on
make php-shell

# In Container:
export XDEBUG_CONFIG="idekey=vscode"
php artisan test --filter="TestName"
```

IDE muss auf Port 9003 lauschen!

### **Variante B: Mit var_dump() / dd()

```bash
make debug-on
make php-shell

# Im Code:
dd($variable);  // Laravel Dump & Die
// oder
var_dump($variable);  // PHP Standard
```

### **Variante C: Mit Logging**

```bash
make debug-on
make php-shell

# Im Code:
Log::info('Debug: ', ['data' => $variable]);

# Logs anzeigen:
tail -f storage/logs/laravel.log
```

---

## 🧪 Tests mit Xdebug

### **Feature-Tests debuggen:**

```bash
make debug-on
make php-shell

# In Container:
vendor/bin/pest tests/Feature/AnalyzeControllerTest.php
```

### **Coverage-Reports generieren:**

```bash
make debug-on
vendor/bin/pest --coverage --min=90
```

---

## 📊 Performance-Vergleich

| Modus | Speed | Code Coverage | Debugger |
|-------|-------|---------------|----------|
| **debug-off** | ✅ 1x (normal) | ❌ Nein | ❌ Nein |
| **debug-on** | 🐢 0.5x (50% langsamer) | ✅ Ja | ✅ Ja |

**Empfehlung:**
- Normale Entwicklung & Tests: `make debug-off`
- Debugging nötig: `make debug-on`

---

## 🔍 Troubleshooting

### **Breakpoint wird nicht erreicht?**

1. IDE muss auf Port 9003 lauschen
2. VSCode: "Listen for Xdebug" starten
3. PhpStorm: "Debug" oder "Break on first line" aktivieren
4. Browser: `make debug-test` ausführen

### **"Port 9003 already in use"?**

```bash
# Andere IDE-Session schließen oder anderen Port:
sudo lsof -i :9003
kill -9 <PID>
```

### **Xdebug nicht installiert?**

```bash
make debug-status

# Sollte anzeigen:
# ✅ Xdebug ist INSTALLIERT
```

Wenn nicht: `make debug-on` erneut ausführen.

### **Performance-Probleme?**

Das ist normal! Xdebug ist ~50% langsamer.

Für normale Entwicklung: `make debug-off`

### **Logs anzeigen?**

```bash
make debug-logs
```

---

## 📚 Weitere Ressourcen

- [Xdebug Official Docs](https://xdebug.org/)
- [VSCode PHP Debug Extension](https://github.com/felixbecker/vscode-php-debug)
- [PhpStorm Debugging](https://www.jetbrains.com/help/phpstorm/debugging-code.html)

---

## ✅ Checkliste zum Starten

- [ ] Xdebug-Erweiterung in IDE installiert
- [ ] `make debug-on` ausgeführt
- [ ] `make debug-status` zeigt "✅ Xdebug ist INSTALLIERT"
- [ ] IDE lauscht auf Port 9003
- [ ] Breakpoint gesetzt
- [ ] Script ausgeführt → Debugger stoppt

**Viel Spaß beim Debuggen!** 🎉

