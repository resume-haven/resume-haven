# Commit 22 – Anonyme CV-Speicherung (Profile Context)

**Branch:** `feature/commit-22-profile-cv-storage`  
**Status:** 🔄 In Umsetzung (Basis implementiert)  
**Erstellt:** 2026-03-10

---

> **Umsetzungsstand:** Siehe `docs/COMMIT_22_IMPLEMENTATION_GUIDE.md` fuer den bereits implementierten Basis-Flow und die verifizierten Quality Gates.

---

## 🎯 Ziel

Implementierung eines neuen Bounded Context `Profile` für anonyme CV-Speicherung und -Wiederherstellung über URL-Token. User können ihren CV speichern und über einen sicheren Link später wiederverwenden, ohne User-Account.

---

## ✅ Scope (MVP)

### Funktional
- ✅ CV speichern (verschlüsselt, token-basiert)
- ✅ CV laden über URL-Token
- ✅ Token-Generierung (URL-safe Base64, nicht erratbar)
- ✅ Verschlüsselung mit Token als Secret (MVP-Kompromiss)
- ✅ Unbegrenzte Gültigkeit (kein TTL im MVP)
- ✅ UI-Integration in `/analyze` (Speichern/Laden-Buttons)

### Technisch
- ✅ Neuer Bounded Context `Profile` (`app/Domains/Profile/`)
- ✅ CQRS: `StoreResumeCommand` + `GetResumeByTokenQuery`
- ✅ Single-Action-Controller (`StoreResumeController`, `LoadResumeController`)
- ✅ Repository Pattern (`ProfileRepository`)
- ✅ Migration + Model (`StoredResume`)
- ✅ Immutable DTOs (`StoreResumeDto`, `ResumeTokenDto`)
- ✅ Pest Tests (Feature + Unit + Security)
- ✅ PHPStan Level 9 + Pint konform

---

## 🚫 Nicht im Scope (MVP)

### Funktional
- ❌ User-Accounts (kommt später)
- ❌ Mehrere CVs pro User (nur 1 CV pro Token)
- ❌ CV-Verlauf / Historie
- ❌ CV-Bearbeitung (nur neu speichern)
- ❌ TTL / Ablaufdatum (unbegrenzt im MVP)
- ❌ Manuelle Token-Wahl (nur automatisch generiert)

### Technisch
- ❌ Separate Encryption Keys pro User
- ❌ Key Rotation
- ❌ Audit Log für Zugriffe
- ❌ Rate Limiting auf Storage

---

## 🏗️ Architektur-Entscheidungen

### 1. Token-Design

**Entscheidung:** URL-safe Base64, 32 Bytes zufällig

```php
// Beispiel-Token
$token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
// Ergebnis: "xK8vQmP3nR-_7dY2..." (~43 Zeichen)
```

**Begründung:**
- ✅ Nicht erratbar (256 Bit Entropie)
- ✅ URL-safe (keine Probleme mit `+`/`/`)
- ✅ Kompakt (~43 Zeichen)
- ✅ Standard PHP-Funktionen

**Security:**
- Token ist URL-Parameter: HTTPS Pflicht (Production)
- Kein Token-Reuse möglich (jeder Speichervorgang = neuer Token)
- Token-Brute-Force praktisch unmöglich

---

### 2. Verschlüsselung (MVP-Kompromiss)

**Entscheidung:** Token dient im MVP als Basis fuer das Encryption Secret

```php
$key = hash('sha256', $token, true);
$iv = random_bytes(12);
$cipherText = openssl_encrypt($cvText, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
```

**Begründung (MVP):**
- ✅ Keine separate Key-Verwaltung nötig
- ✅ Token muss sowieso sicher und nicht erratbar sein
- ✅ Daten at-rest verschlüsselt
- ✅ Robuste, lokale Implementierung ohne zusätzlichen Infrastrukturbedarf

**⚠️ Technische Schuld:**
- ❌ Token-Verlust = Datenverlust (keine Recovery)
- ❌ Keine Key-Rotation möglich
- ❌ User-basiertes Modell später benötigt Refactoring

**🔄 Migration zu User-basierter Verschlüsselung (Post-MVP):**

**WICHTIG:** Diese Planung ist **verpflichtend** vor Implementierung von User-Accounts durchzuführen!

**Zielarchitektur (Phase 3, ~Commit 35+):**
```
User-Modell mit separatem Encryption Key
├─ User besitzt Master Key (verschlüsselt mit Passwort)
├─ CV verschlüsselt mit Master Key (nicht mit URL-Token)
├─ Token nur für Authentifizierung/Freigabe
└─ Key Rotation möglich
```

**Migrations-Schritte (grob):**

1. **Planung (Pflicht vor Commit ~35):**
   - Threat-Modelling für Key-Management
   - Entscheidung: Key Derivation (PBKDF2/Argon2) vs. HSM
   - Recovery-Strategie (Backup-Codes? E-Mail-Reset?)
   - Data-Migration-Plan für bestehende anonyme CVs

2. **Implementierung:**
   - Neue `users` Tabelle mit `encryption_key_hash`
   - `stored_resumes` Relation zu `users` (nullable für Migration)
   - Re-Encryption Job für alte Token-basierte CVs
   - Auth-Middleware für Profile-Routen

3. **Testing:**
   - Backwards-Compatibility für anonyme CVs
   - Key-Rotation-Tests
   - Recovery-Flow-Tests

4. **Dokumentation:**
   - Security-Audit der neuen Architektur
   - User-Communication über Änderungen
   - Migration-Guide für bestehende Tokens

**Status:** ⏳ **Noch nicht geplant** (kommt vor User-Accounts)

---

### 3. Bounded Context `Profile`

**Entscheidung:** Neuer Context neben `Analysis`

**Struktur:**
```
app/Domains/Profile/
├── Commands/
│   └── StoreResumeCommand.php
├── Queries/
│   └── GetResumeByTokenQuery.php
├── Handlers/
│   ├── StoreResumeHandler.php
│   └── GetResumeByTokenHandler.php
├── Actions/
│   ├── GenerateTokenAction.php
│   ├── EncryptResumeAction.php
│   └── DecryptResumeAction.php
├── Repositories/
│   └── ProfileRepository.php
└── Dto/
    ├── StoreResumeDto.php
    ├── ResumeTokenDto.php
    └── LoadedResumeDto.php
```

**Context-Abgrenzung:**
- **`Profile`**: CV-Speicherung, -Laden, Token-Verwaltung
- **`Analysis`**: Bleibt unverändert, nutzt CV-Text als Input
- **Interaction**: `Analysis` kennt `Profile` nicht (Entkopplung)

---

## 📋 Implementierungs-Phasen

Siehe vollständige Code-Beispiele in `COMMIT_PLAN.md` (Commit 22 Abschnitt).

### Phase 1: Domain-Struktur & Datenmodell (~30min)
- Migration `create_stored_resumes_table`
  - Felder: `token`, `encrypted_cv`, `last_accessed_at`, `timestamps`
- Model `StoredResume`
- Context-Verzeichnisstruktur anlegen

### Phase 2: Domain-Logic (CQRS) (~2h)
- DTOs (immutable, readonly)
- Actions (Generate, Encrypt, Decrypt)
- Repository (store, getByToken, touchLastAccessed)
- Command + Handler (Write)
- Query + Handler (Read)

### Phase 3: HTTP-Layer (~1h)
- Routes: `POST /profile/store`, `GET /profile/load/{token}`
- Single-Action-Controller
- FormRequest (Validierung)

### Phase 4: UI-Integration (~1h)
- Analyze-View: "💾 CV speichern" Button
- JavaScript: Async POST, Token-Link kopieren
- Success/Error-Messages
- CV-Laden automatisch bei Token-URL

### Phase 5: Tests (~1.5h)
- Feature: Speichern/Laden (Happy Path + Errors)
- Unit: Token-Gen, Encrypt/Decrypt, Repository
- Security: Token-Uniqueness, SQL-Injection, Brute-Force

---

## ✅ Definition of Done (DoD)

### Funktional
- [ ] CV kann über UI gespeichert werden
- [ ] Token wird generiert und angezeigt
- [ ] CV kann über Token-Link geladen werden
- [ ] CV-Text wird in Analyze-Form eingetragen
- [ ] Fehlerbehandlung für ungültige/fehlende Tokens
- [ ] Fehlerbehandlung für Entschlüsselungsfehler

### Technisch
- [ ] Migration erstellt und ausgeführt
- [ ] Model `StoredResume` erstellt
- [ ] Bounded Context `Profile` strukturiert
- [ ] CQRS: Command + Query + Handlers implementiert
- [ ] Actions implementiert (Generate, Encrypt, Decrypt)
- [ ] Repository implementiert
- [ ] Single-Action-Controller implementiert
- [ ] Routes registriert
- [ ] UI integriert (Speichern/Laden-Buttons)

### Tests
- [ ] Feature-Tests: Speichern/Laden (Happy Path)
- [ ] Feature-Tests: Fehlerszenarien (ungültige Tokens, zu kurzer CV)
- [ ] Unit-Tests: Token-Generierung
- [ ] Unit-Tests: Verschlüsselung/Entschlüsselung
- [ ] Security-Tests: Token-Uniqueness, SQL-Injection
- [ ] Alle Tests grün (100% Pass)

### Quality-Gates
- [ ] PHPStan Level 9: 0 Errors
- [ ] Pint: Code-Style konform
- [ ] Test-Coverage ≥ 95%
- [ ] Dokumentation aktualisiert (`ARCHITECTURE.md`, `CODING_GUIDELINES.md`)

---

## 📚 Dokumentations-Updates

### `docs/ARCHITECTURE.md`
- [ ] Bounded Context `Profile` hinzufügen
- [ ] CQRS-Struktur dokumentieren
- [ ] Context-Abgrenzung zu `Analysis` erklären
- [ ] Migrations-Hinweis für User-basierte Verschlüsselung

### `docs/CODING_GUIDELINES.md`
- [ ] Krypto-Regeln für Commit 22 dokumentieren
- [ ] Token-Format (URL-safe Base64) festlegen
- [ ] MVP-Kompromisse klar markieren

### `docs/ai/PROJECT_OVERVIEW.md`
- [ ] "NICHT im MVP" aktualisieren (CV-Speicherung → ✅)
- [ ] Roadmap: User-basierte Verschlüsselung als Pflichtschritt aufnehmen

---

## 🐛 Bekannte Limitierungen (MVP)

### Security
- ⚠️ Token-Verlust = Datenverlust (keine Recovery)
- ⚠️ Keine Key-Rotation möglich
- ⚠️ Token-Sharing = voller Zugriff (kein Schutz)

### Funktional
- ⚠️ Kein TTL (unbegrenzte Speicherung)
- ⚠️ Keine Multi-CV-Verwaltung
- ⚠️ Keine CV-Historie

### Performance
- ⚠️ Keine Cleanup-Routine für alte CVs (kommt später)

---

## 🔄 Zukünftige Erweiterungen (Post-MVP)

### Phase 3 (~Commit 35+): User-Accounts & sichere Verschlüsselung
- **VOR Implementierung:** Detaillierte Planung verpflichtend!
- User-basierte Master Keys
- Key Derivation (PBKDF2/Argon2)
- Recovery-Mechanismus
- Re-Encryption für alte anonyme CVs

### Phase 4 (~Commit 40+): CV-Management
- Mehrere CVs pro User
- CV-Versionierung
- CV-Templates
- Export-Funktionen

---

## ⏱️ Geschätzter Aufwand

- **Phase 1 (Datenmodell):** ~30min
- **Phase 2 (Domain-Logic):** ~2h
- **Phase 3 (HTTP-Layer):** ~1h
- **Phase 4 (UI-Integration):** ~1h
- **Phase 5 (Tests):** ~1.5h
- **Dokumentation:** ~30min
- **Gesamt:** ~6.5h

---

**Letzte Aktualisierung:** 2026-03-10  
**Version:** 1.0 (Detaillierte Planung für Commit 22)

