# Technical Debt & ToDos

Dieses Dokument trackt bekannte technische Schulden, offene Aufgaben und geplante Verbesserungen für ResumeHaven.

## 🔴 High Priority

### Dependencies Updates

#### driftingly/rector-laravel Update
**Status:** Offen  
**Priorität:** Hoch  
**Beschreibung:** Update von `driftingly/rector-laravel` auf die aktuellste Version

**Abhängigkeiten:**
- Erfordert Update von `webmozart/assert` auf Version > 2.1

**Aktuelle Version:** 2.1  
**Zielversion:** Neueste stabile Version

**Schritte:**
1. Update `webmozart/assert` auf > 2.1 in composer.json
2. Update `driftingly/rector-laravel` auf neueste Version
3. `composer update driftingly/rector-laravel webmozart/assert`
4. Rector-Konfiguration testen (`make rector`)
5. Eventuelle Breaking Changes dokumentieren

**Commit Message:**
```
chore(deps): update rector-laravel and webmozart/assert

- Update driftingly/rector-laravel to latest version
- Update webmozart/assert to >2.1 (required dependency)
- Test rector configuration after update
```

---

## 🟡 Medium Priority

### Code Quality

- [ ] PHPStan Level erhöhen (aktuell: TBD, Ziel: 8)
- [ ] Pest Coverage auf > 80% bringen
- [ ] Mutation Testing mit Pest einrichten
- [ ] Profanity Testing mit Pest einrichten

### Documentation

- [ ] API Documentation generieren (z.B. mit phpDocumentor)
- [ ] Architecture Decision Records (ADRs) erstellen
- [ ] Onboarding-Guide für neue Entwickler

### Performance

- [ ] Laravel Octane evaluieren
- [ ] Database Query Optimization
- [ ] Caching-Strategie implementieren

---

## 🟢 Low Priority / Nice to Have

### Developer Experience

- [ ] Pre-commit Hooks einrichten (Husky oder ähnlich)
- [ ] IDE Helper für bessere Auto-completion
- [ ] Docker-Setup für verschiedene PHP-Versionen

### Testing

- [ ] E2E Tests mit Laravel Dusk
- [ ] Performance Tests einrichten
- [ ] Security Testing automatisieren

### CI/CD

- [ ] Deployment Automation verbessern
- [ ] Staging Environment einrichten
- [ ] Automated Rollback-Mechanismus

---

## ✅ Completed

### 2026-02-03
- [x] Rector auf PHP 8.5 konfiguriert
- [x] Laravel 12.0 Rulesets in Rector integriert
- [x] Dokumentation für Code Quality aktualisiert
- [x] Makefile und Composer Scripts für Rector erstellt

---

## Notes

### Version Requirements
- **PHP:** 8.5+
- **Laravel:** 12.0+
- **Pest:** Neueste stabile Version
- **Rector:** 2.3+

### Breaking Changes Checklist
Beim Update von Major-Versionen beachten:
1. CHANGELOG der betroffenen Packages prüfen
2. Deprecation Warnings beheben
3. Tests ausführen (`make test`)
4. Code Quality Checks (`make quality`)
5. Dokumentation aktualisieren

---

## How to Add Items

**Format:**
```markdown
### [Titel der Aufgabe]
**Status:** [Offen/In Progress/Blocked/Completed]
**Priorität:** [Hoch/Mittel/Niedrig]
**Beschreibung:** [Kurze Beschreibung]

**Schritte:**
1. Schritt 1
2. Schritt 2
...
```

**Kategorien:**
- Dependencies Updates
- Code Quality
- Documentation
- Performance
- Developer Experience
- Testing
- CI/CD
- Security
- Infrastructure
