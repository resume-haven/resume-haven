# Architecture Tests

Architecture Tests prüfen die Struktur und Architektur des Codes automatisch und stellen sicher, dass definierte Regeln eingehalten werden.

## Pest ArchPresets

Dieses Projekt verwendet die offiziellen **Pest ArchPresets**, die Best Practices für PHP und Laravel bündeln:

### `arch()->preset()->laravel()`
Das Laravel-Preset enthält bewährte Laravel-Konventionen:
- ✅ Traits in `App\Traits` und `App\Concerns`
- ✅ Enums in `App\Enums`
- ✅ Models erweitern Eloquent Model
- ✅ Controllers mit korrektem Suffix und Laravel-Methoden
- ✅ Middleware, Policies, Commands mit korrekten Namenskonventionen
- ✅ Keine Debugging-Funktionen (dd, ddd, dump, ray, env, exit)
- ✅ Attributes implementieren ContextualAttribute

### `arch()->preset()->security()`
Das Security-Preset verhindert unsichere Funktionen:
- ❌ Schwache Hash-Funktionen: `md5`, `sha1`
- ❌ Unsichere Zufallsfunktionen: `uniqid`, `rand`, `mt_rand`, `str_shuffle`
- ❌ Code-Ausführung: `eval`, `exec`, `shell_exec`, `system`, `passthru`, `create_function`
- ❌ Unsichere Funktionen: `unserialize`, `extract`, `parse_str`, `mb_parse_str`, `assert`, `dl`

### `arch()->preset()->php()`
Das PHP-Preset verhindert veraltete und schlechte Praktiken:
- ❌ Debugging: `var_dump`, `print_r`, `debug_*`, `die`, `phpinfo`, `echo`
- ❌ Veraltete MySQL-Funktionen: `mysql_*`
- ❌ Schlechte Praktiken: `goto`, `global`
- ❌ Veraltete Regex: `ereg`, `eregi`

### `arch()->preset()->strict()`
Das Strict-Preset erzwingt strenge Code-Qualität:
- ✅ Strict Types in allen Dateien
- ✅ Strict Equality (`===` statt `==`)
- ✅ Finale Klassen (keine Vererbung ohne Absicht)
- ✅ Keine protected Methods (bevorzugt private oder public)
- ✅ Keine abstract Klassen (bevorzugt Interfaces)
- ❌ Keine `sleep`, `usleep` (blockierende Funktionen)

## Test-Dateien

### GeneralTest.php
- Verwendet: `arch()->preset()->laravel()`, `arch()->preset()->php()`
- Zusätzliche projektspezifische Regeln für Value Objects und DTOs

### SecurityTest.php
- Verwendet: `arch()->preset()->security()`
- Zusätzliche Laravel-Security-Regeln (Raw SQL, fillable/guarded, CSRF)

### StrictTest.php
- Verwendet: `arch()->preset()->strict()`
- Erzwingt höchste Code-Qualität

### LayerTest.php
Prüft die Layer-Architektur (DDD):
- ✅ Domain Layer ist unabhängig von Infrastructure
- ✅ Domain Layer kennt keine UI-Details
- ✅ Application Layer nutzt Domain Layer
- ✅ Infrastructure implementiert Domain Interfaces
- ✅ Controller nutzen Application Services
- ✅ Kein direkter DB-Zugriff in Controllern

### SolidTest.php
Prüft SOLID-Prinzipien:
- ✅ **I**nterface Segregation: Interfaces sind fokussiert (max. 5 Methoden)
- ✅ **D**ependency Inversion: Abhängigkeit von Abstraktionen, nicht Implementierungen

## Ausführung

```bash
# Alle Architecture Tests
make test-architecture

# Einzelne Test-Datei
docker-compose exec app ./vendor/bin/pest tests/Architecture/GeneralTest.php

# Mit --bail (stoppt beim ersten Fehler)
docker-compose exec app ./vendor/bin/pest tests/Architecture --bail
```

## Erweitern

Um neue Architektur-Regeln hinzuzufügen:

```php
<?php

arch('beschreibung der regel')
    ->expect('App\Namespace')
    ->toUseStrictTypes()
    ->not->toUse('Verbotene\Klasse');
```

### Verfügbare Expectations

**Code Standards:**
- `toUseStrictTypes()` - Strict types deklariert
- `toBeFinal()` - Klasse ist final
- `toBeReadonly()` - Klasse ist readonly (PHP 8.2+)
- `toBeAbstract()` - Klasse ist abstrakt
- `toBeInterface()` - Ist Interface

**Naming:**
- `toHaveSuffix('Suffix')` - Klassen haben Suffix
- `toHavePrefix('Prefix')` - Klassen haben Prefix
- `toMatch('/Pattern/')` - Name matched Pattern

**Dependencies:**
- `toUse('Namespace')` - Nutzt Namespace
- `not->toUse('Namespace')` - Nutzt NICHT Namespace
- `toOnlyUse(['Allowed'])` - Nutzt NUR erlaubte Namespaces
- `toOnlyBeUsedIn(['Allowed'])` - Wird nur in bestimmten Namespaces genutzt

**Inheritance:**
- `toExtend('BaseClass')` - Erweitert Base Class
- `toImplement('Interface')` - Implementiert Interface
- `toExtendNothing()` - Erweitert nichts

**Methods & Properties:**
- `toHaveMethod('methodName')` - Hat Methode
- `toHaveProperty('propertyName')` - Hat Property
- `toHaveMaximumMethodCount(5)` - Max. Anzahl Methoden

## Best Practices

1. **Spezifisch bleiben**: Teste konkrete Regeln, nicht allgemeine Aussagen
2. **Ignorieren wenn nötig**: Nutze `->ignoring()` für Ausnahmen
3. **Kombinieren**: Nutze `and()` und `or()` für mehrere Bedingungen
4. **Dokumentieren**: Jeder Test sollte eine klare Beschreibung haben
5. **Iterativ erweitern**: Füge Tests hinzu während das Projekt wächst

## Integration in CI/CD

Architecture Tests laufen automatisch bei:
- `make quality` - Code Quality Check
- `make test` - Alle Tests
- GitHub Actions CI/CD Pipeline

## Weitere Informationen

- [Pest Architecture Testing Docs](https://pestphp.com/docs/arch-testing)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)
