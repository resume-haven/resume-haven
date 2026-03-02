a# PHPStan - Statische Code-Analyse

## Übersicht

PHPStan ist ein statischer Code-Analyzer für PHP, der Fehler findet, ohne den Code auszuführen. 
Für dieses Projekt ist PHPStan auf **Level 9** konfiguriert (höchstes Level).

## Installation

PHPStan ist bereits in der `composer.json` unter `require-dev` eingebunden:

```bash
cd src
composer install
```

## Konfiguration

Die Konfiguration befindet sich in der Datei `phpstan.neon`:

- **Level**: 9 (strengstes Level)
- **Analysierte Pfade**: `app/`, `routes/`, `config/`, `bootstrap/`
- **Ausgeschlossene Pfade**: `tests/`, `vendor/`, `storage/`

## Verwendung

### Analyse durchführen

```bash
# Via Make (empfohlen)
make phpstan

# Oder direkter Composer-Befehl (im Container)
docker exec -it resumehaven-php composer run phpstan

# Oder direkt (im Container)
docker exec resumehaven-php vendor/bin/phpstan analyse
```

### Baseline generieren

Eine Baseline wird benötigt, um bekannte Fehler zu ignorieren:

```bash
# Via Make
make phpstan-baseline

# Oder direkter Composer-Befehl (im Container)
docker exec -it resumehaven-php composer run phpstan:baseline

# Oder direkt (im Container)
docker exec resumehaven-php vendor/bin/phpstan analyse --generate-baseline
```

Diese erstellt die Datei `.phpstan.baseline.neon` (in .gitignore).

## Best Practices

1. **Regelmäßig testen**: Führe PHPStan vor jedem Commit aus
2. **Baseline aktualisieren**: Nach größeren Refactorings die Baseline neu generieren
3. **Fehler beheben statt ignorieren**: Versuche, echte Fehler zu beheben statt zu ignorieren

## Fehlerkategorien

PHPStan auf Level 9 prüft u.a.:

- Undefinierten Code (Funktionen, Methoden, Properties)
- Falsche Typhinweise
- Null-Pointer-Fehler
- Rückgabetyp-Mismatches
- Dead Code
- Generische Typen-Validierung

## Troubleshooting

### "no such table" Fehler während Tests

PHPStan benötigt die Datenbank für statische Analyse nicht. Diese Fehler treten nur bei Unit-Tests auf und sollten durch Mocking der Services behoben werden.

### Performance-Probleme

Wenn PHPStan langsam läuft:

1. Cache überprüfen: `storage/phpstan/`
2. Container neustarten: `docker compose restart resumehaven-php`
3. Level reduzieren (nicht empfohlen): `level: 8` in `phpstan.neon`

## Integration in CI/CD

PHPStan kann in automatisierten Workflows integriert werden:

```bash
composer run phpstan
```

Falls die Analyse fehlschlägt, werden Fehler gemeldet und der Exit-Code ist ungleich 0.

