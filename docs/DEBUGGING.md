# 🐛 Debugging with Xdebug

Complete guide to local debugging with Xdebug in Docker.

---

## ⚡ Quick Start

```bash
make debug-on       # Xdebug aktivieren
make debug-status   # Status prüfen
make php-shell      # In Container gehen (XDEBUG_CONFIG ist bereits gesetzt!)
php artisan tinker  # Debuggen!
```

---

## 📋 Available commands

| Command | Description |
|----------|-------------|
| `make debug-on` | Enable Xdebug (rebuild with override.yml) |
| `make debug-off` | Disable Xdebug (normal fast mode) |
| `make debug-status` | Check Xdebug status |
| `make debug-test` | Send test request with XDEBUG_SESSION cookie |
| `make debug-logs` | Show Xdebug logs |
| `make test-coverage` | Check coverage in console (min 80%) |
| `make test-coverage-report` | Generate coverage files (`src/coverage-report/`) |

---

## 🔧 How it works

### **Architecture:**

1. **Build time (`make debug-on`):**
   - `docker-compose.override.yml` is created/copied
   - PHP container built with `INSTALL_XDEBUG=true`
   - Xdebug is compiled via `pecl install xdebug`

2. **Xdebug configuration (only if installed):**
```ini
[xdebug]
; Modi: debug (Debugger), coverage (Code-Coverage)
xdebug.mode=debug,coverage
xdebug.start_with_request=yes
xdebug.discover_client_host=true
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.idekey=resumehaven
```

**Runtime Config:**
- `XDEBUG_MODE=debug,coverage` (for debugging + coverage)
- Environment variable is set automatically via `docker-compose.override.yml`

3. **Disable (`make debug-off`):**
   - `docker-compose.override.yml` is deleted
   - Rebuild without Xdebug
   - Everything runs 50% faster

---

## 🎯 VSCode Setup

### **1. Install extension**

- Install: "PHP Debug" (Felix Becker) or "PHP Intelephense"

### **2. Launch configuration**

The file `.vscode/launch.json` will be created automatically, should contain:

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

### **3. Debug**

1. **Enable Xdebug:**
   ```bash
   make debug-on
   ```

2. **Set breakpoint:** Click line (red dot)

3. **Start debugger:** VSCode → Run and Debug (Ctrl+Shift+D) → "Listen for Xdebug (Docker)"

4. **Run script:**
   ```bash
   make php-shell
   php artisan tinker
   ```
→ Debugger stops at breakpoint!

5. **Debugging Controls:**
   - ▶ Continue (F5)
   - ⊙ Step Over (F10)
   - ↘ Step Into (F11)
   - ↖ Step Out (Shift+F11)

---

## 🎯 PhpStorm Setup

### **1. Configure server**

- `Preferences → Languages & Frameworks → PHP → Servers`
- **New Servers:**
  - Name: `localhost`
  - Host: `localhost`
  - Port: `8080`
  - Debugger: `Xdebug`
  - Path mapping:
    - `/var/www/html` → `<project>/src`

### **2. Debug configuration**

- `Run → Edit Configurations`
- **New PHP Remote Debug:**
  - Server: `localhost`
  - Port: `9003`

### **3. Debug**

1. **Enable Xdebug:**
   ```bash
   make debug-on
   ```

2. **Set breakpoint:** Click line

3. **Start debugger:**
   - `Run → Debug 'PHP Remote Debug'` (Shift+F9)
   - or: `Run → Break on first line`

4. **Run script:**
   ```bash
   make php-shell
   php artisan test
   ```
→ Debugger stops at breakpoint!

---

## 💻 CLI debugging (With and without IDE)

### **Variant A: With IDE**

```bash
make debug-on
make php-shell

# In Container:
export XDEBUG_CONFIG="idekey=vscode"
php artisan test --filter="TestName"
```

IDE must listen on port 9003!

### **Variant B: With var_dump() / dd()

```bash
make debug-on
make php-shell

# Im Code:
dd($variable);  // Laravel Dump & Die
// oder
var_dump($variable);  // PHP Standard
```

### **Variant C: With logging**

```bash
make debug-on
make php-shell

# Im Code:
Log::info('Debug: ', ['data' => $variable]);

# Logs anzeigen:
tail -f storage/logs/laravel.log
```

---

## 🧪 Testing with Xdebug

### **Debug feature tests:**

```bash
make debug-on
make php-shell

# In Container:
vendor/bin/pest tests/Feature/AnalyzeControllerTest.php
```

### **Generate coverage reports:**

```bash
make debug-on                      # Xdebug mit coverage-Mode aktivieren
make test-coverage                 # Coverage-Check in Konsole (min 95%)
make test-coverage-report          # Dateien erzeugen (Clover + HTML)
make coverage-open                 # HTML-Report im Browser öffnen
make coverage-clean                # Alte Reports löschen
```

**Coverage files in the file system:**

- `src/coverage-report/clover.xml`
- `src/coverage-report/html/index.html`

**Current coverage:**
- **Total:** 98.2% ✅
- **Minimum:** 95%

---

## 📊 Performance comparison

| Fashion | Speed ​​| Code Coverage | Debugger | Usage |
|-------|-------|---------------|----------|------------|
| **debug off** | ✅ 1x (normal) | ❌ No | ❌ No | Normal development |
| **debug on** | 🐢 0.5x (50% slower) | ✅ Yes | ✅ Yes | Debugging + Coverage |

**Recommendation:**
- Normal Development & Testing: `make debug-off`
- Debugging required: `make debug-on`
- Coverage check: `make test-coverage`
- Coverage files: `make test-coverage-report`

---

## 🔍 Troubleshooting

### **Breakpoint is not reached?**

1. IDE must listen on port 9003
2. VSCode: Start “Listen for Xdebug”.
3. PhpStorm: Enable “Debug” or “Break on first line”.
4. Browser: Run `make debug-test`

### **"Port 9003 already in use"?**

```bash
# Andere IDE-Session schließen oder anderen Port:
sudo lsof -i :9003
kill -9 <PID>
```

### **Xdebug not installed?**

```bash
make debug-status

# Sollte anzeigen:
# ✅ Xdebug ist INSTALLIERT
```

If not: run `make debug-on` again.

### **Performance issues?**

This is normal! Xdebug is ~50% slower.

For normal development: `make debug-off`

### **Show logs?**

```bash
make debug-logs
```

---

## 📚 More resources

- [Xdebug Official Docs](https://xdebug.org/)
- [VSCode PHP Debug Extension](https://github.com/felixbecker/vscode-php-debug)
- [PhpStorm Debugging](https://www.jetbrains.com/help/phpstorm/debugging-code.html)

---

## ✅ Starting checklist

- [ ] Xdebug extension installed in IDE
- [ ] `make debug-on` executed
- [ ] `make debug-status` shows "✅ Xdebug is INSTALLED"
- [ ] IDE listens on port 9003
- [ ] Breakpoint set
- [ ] Script executed → Debugger stops

**Happy debugging!** 🎉