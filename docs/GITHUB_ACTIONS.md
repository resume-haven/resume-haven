# GitHub Actions Workflows

Dieses Projekt verwendet GitHub Actions für automatisierte Code-Qualität und Sicherheitsprüfungen.

## 📋 Workflows

### 1. **Code Quality** (`code-quality.yml`)
Automatische Code-Qualitätsprüfung bei jedem Push und Pull Request.

**Jobs:**
- ✅ **Lint** - Code Style mit Pint
- ✅ **Static Analysis** - PHPStan mit Larastan
- ✅ **Refactoring Check** - Rector dry-run
- ✅ **Tests** - Unit, Feature, Integration, Architecture Tests

**Trigger:**
- Push auf `main`, `develop`
- Pull Requests gegen `main`, `develop`
- Täglich um 2:00 UTC

**Features:**
- Parallele Job-Ausführung (schneller)
- Composer Dependency Caching
- SQLite In-Memory Database
- Fehlerhafte Jobs stoppen weitere nicht (fail-fast: false)

### 2. **Security** (`security.yml`)
Fokussierte Sicherheitsprüfungen und Dependency-Validierung.

**Jobs:**
- ✅ **Architecture Security Tests** - Pest Architecture Preset
- ✅ **Composer Validation** - `composer.json` und `composer.lock` validieren
- ✅ **PHP Syntax Check** - Syntax-Fehler erkennen

**Trigger:**
- Push und Pull Requests
- Täglich um 3:00 UTC

### 3. **CI Pipeline** (`ci.yml`)
Vollständiger CI-Pipeline mit abhängigen Jobs.

**Job-Abhängigkeiten:**
```
validation
    ├── lint
    ├── analysis
    └── tests
         └── status (final check)
```

**Trigger:**
- Push auf `main`, `develop`
- Pull Requests gegen `main`, `develop`

**Features:**
- Abhängige Jobs (nur ausführen wenn vorherige erfolgreich)
- Schnelleres Feedback bei Validierungsfehlern
- Parallele Ausführung von lint, analysis, tests

## 🚀 Features

### Kostenlos
✅ Alle Workflows nutzen **kostenlose GitHub Actions**
- Ubuntu Latest Runner (kostenfrei)
- Standard Actions (kostenlos)
- Keine Third-Party kostenpflichtigen Tools

### Performance
✅ **Composer Dependency Caching**
- Cache basiert auf `composer.lock`
- Spart ~30-60 Sekunden pro Run

✅ **Parallele Job-Ausführung**
- Lint, Analysis, Tests laufen parallel
- Total Runtime: ~5-10 Minuten

✅ **Fail-Fast für Tests**
- Architektur Tests stoppen bei erstem Fehler (--bail)
- Schnelleres Feedback

### Zuverlässigkeit
✅ **Concurrency Control**
- Nur ein Workflow pro Branch
- Ältere Runs werden abgebrochen

✅ **Scheduled Runs**
- Sicherheitsprüfungen täglich
- Erkennt Dependency-Probleme früh

## 📊 Verwendete Tools

| Tool | Job | Aktion |
|------|-----|--------|
| **Pint** | Lint | `composer test:lint` |
| **PHPStan** | Analysis | `composer test:phpstan` |
| **Rector** | Analysis | `composer test:rector` |
| **Pest** | Tests | `composer test:*` |

Alle Tools sind in `composer.json` Scripts definiert.

## 🔄 Workflow Status Badges

Füge diese Badges in deine `README.md` ein:

```markdown
[![Code Quality](https://github.com/username/resume-haven/actions/workflows/code-quality.yml/badge.svg)](https://github.com/username/resume-haven/actions/workflows/code-quality.yml)
[![CI](https://github.com/username/resume-haven/actions/workflows/ci.yml/badge.svg)](https://github.com/username/resume-haven/actions/workflows/ci.yml)
[![Security](https://github.com/username/resume-haven/actions/workflows/security.yml/badge.svg)](https://github.com/username/resume-haven/actions/workflows/security.yml)
```

## 📝 Anpassungen

### Branches ändern
Ändere `branches: [ main, develop ]` in den Workflows, um andere Branches zu nutzen.

### PHP-Version ändern
Ändere `php-version: '8.5'` in den Workflows für andere PHP-Versionen.

### Schedule ändern
Cron-Syntax für geplante Runs:
```yaml
schedule:
  - cron: '0 2 * * *'  # täglich um 02:00 UTC
  - cron: '0 */6 * * *'  # alle 6 Stunden
  - cron: '0 0 * * 0'  # jeden Sonntag um 00:00 UTC
```

## 🎯 Best Practices

1. **Branches schützen**: Erlaubt Merges nur wenn all Checks bestanden
   - GitHub Settings → Branches → Add Rule
   - Aktiviere "Require status checks to pass"

2. **Notifications**: Konfiguriere GitHub Notifications für fehlgeschlagene Runs

3. **Artifacts**: Logs sind 90 Tage verfügbar

4. **Secrets**: Keine Secrets in Workflows (nicht vorhanden in diesem Projekt)

## 🐛 Troubleshooting

### Workflows werden nicht ausgeführt
- Prüfe `.github/workflows/` Dateien sind committed
- Branch muss in `on.push.branches` definiert sein
- Prüfe "Actions" Tab auf Fehler

### Cache wird nicht verwendet
- Cache wird nur zwischen Runs geteilt
- Unterschiedliche `composer.lock` = unterschiedliche Cache Keys

### Tests schlagen fehl im CI aber lokal erfolgreich
- Unterschiedliche Umgebung (PHP-Version, Extensions)
- Fehlende `.env` Setup - siehe Workflow `Create .env file`
- SQLite-Fehler - Workflow erstellt `storage/database.sqlite`

## 📚 Weitere Ressourcen

- [GitHub Actions Dokumentation](https://docs.github.com/en/actions)
- [shivammathur/setup-php](https://github.com/shivammathur/setup-php)
- [actions/cache](https://github.com/actions/cache)
