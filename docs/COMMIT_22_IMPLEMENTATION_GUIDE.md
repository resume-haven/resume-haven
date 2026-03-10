# Commit 22 – Implementierungsleitfaden

**Status:** 🔄 Basis implementiert und verifiziert  
**Branch:** `feature/commit-22-profile-cv-storage`

---

## Ziel

Commit 22 fuehrt einen neuen Bounded Context `Profile` ein, mit dem ein Lebenslauf anonym gespeichert und spaeter ueber einen nicht erratbaren Token-Link wieder geladen werden kann.

---

## Umgesetzte Architektur

### Domain
- `app/Domains/Profile/Commands/StoreResumeCommand.php`
- `app/Domains/Profile/Queries/GetResumeByTokenQuery.php`
- `app/Domains/Profile/Handlers/StoreResumeHandler.php`
- `app/Domains/Profile/Handlers/GetResumeByTokenHandler.php`
- `app/Domains/Profile/Actions/GenerateTokenAction.php`
- `app/Domains/Profile/Actions/EncryptResumeAction.php`
- `app/Domains/Profile/Actions/DecryptResumeAction.php`
- `app/Domains/Profile/Repositories/ProfileRepository.php`
- `app/Domains/Profile/Dto/StoreResumeDto.php`
- `app/Domains/Profile/Dto/ResumeTokenDto.php`
- `app/Domains/Profile/Dto/LoadedResumeDto.php`

### Persistence
- Migration: `database/migrations/2026_03_10_140000_create_stored_resumes_table.php`
- Model: `app/Models/StoredResume.php`

### HTTP-Layer
- `app/Http/Requests/StoreResumeRequest.php`
- `app/Http/Controllers/StoreResumeController.php`
- `app/Http/Controllers/LoadResumeController.php`
- Routes in `routes/web.php`

### UI
- Erweiterung von `resources/views/analyze.blade.php`
- CV speichern via POST auf `profile.store`
- CV laden via Token-Link auf `profile.load`
- Session-basierte Success-/Error-Messages

---

## Technische Entscheidungen

### Token
- 32 zufaellige Bytes via `random_bytes()`
- URL-safe Base64 (`+`/`/` -> `-`/`_`, ohne Padding)
- Laenge typischerweise ~43 Zeichen

### Verschluesselung
- AES-256-GCM
- Schluessel wird fuer den MVP aus dem Token via `hash('sha256', $token, true)` abgeleitet
- Payload speichert `iv`, `tag` und `cipher` Base64-kodiert
- Defekte oder ungueltige Payloads werden sicher als `null` behandelt

### Kontext-Grenze
- `Profile` kennt `Analysis` nicht direkt
- Integration erfolgt nur ueber UI-/DTO-Flow: geladener CV-Text wird in das Analyseformular eingetragen

---

## Aktueller User Flow

### CV speichern
1. Nutzer gibt CV-Text in `analyze.blade.php` ein
2. POST auf `route('profile.store')`
3. `StoreResumeRequest` validiert `cv_text`
4. `StoreResumeController` dispatcht `StoreResumeCommand`
5. `StoreResumeHandler` generiert Token, verschluesselt den CV und speichert ihn
6. Redirect auf `route('analyze')` mit `resume_token`, `resume_link`, `success`

### CV laden
1. Nutzer oeffnet `/profile/load/{token}` oder gibt Token auf der Analyze-Seite ein
2. `LoadResumeController` validiert Token-Format
3. Dispatch von `GetResumeByTokenQuery`
4. `GetResumeByTokenHandler` laedt und entschluesselt den CV
5. `last_accessed_at` wird aktualisiert
6. Redirect auf `route('analyze')` mit `loaded_cv`, `loaded_token`, `success`

---

## Tests

### Feature-Tests
- `tests/Feature/ProfileResumeStorageTest.php`
- `tests/Feature/AnalyzeResumeStorageUiTest.php`

### Unit-Tests
- `tests/Unit/GenerateTokenActionTest.php`
- `tests/Unit/ResumeCryptoActionsTest.php`

### Abgesicherte Faelle
- Speichern eines CVs
- Validierungsfehler bei zu kurzem CV
- Laden ueber gueltigen Token
- Fehler bei ungueltigem Token-Format
- Fehler bei unbekanntem Token
- Fehler bei defekter verschluesselter Payload
- Aktualisierung von `last_accessed_at`
- Token-Format und Token-Eindeutigkeit
- Erfolgreiche Ver-/Entschluesselung und Fehlerfall bei falschem Token

---

## Quality Gates (zuletzt verifiziert)

- `make test-feature` ✅
- `make test-unit` ✅
- `make phpstan` ✅
- `make pint-analyse` ✅

---

## Bekannte MVP-Limitierungen

- Kein Copy-to-Clipboard-Komfort fuer den Speicher-Link
- Keine Mehrfachverwaltung von CVs
- Keine TTL / automatische Bereinigung fuer gespeicherte CVs
- Token dient im MVP gleichzeitig als Zugriffstoken und Basis fuer das Secret

---

## Naechste sinnvolle Schritte

1. Copy-to-Clipboard fuer den generierten Speicher-Link
2. Dokumentierte Cleanup-Strategie fuer `stored_resumes`
3. Detailplanung fuer userbasierte Verschluesselung vor Einfuehrung von Accounts
4. Optional spaeter: separater `Profile`-Landing-/Management-Flow

