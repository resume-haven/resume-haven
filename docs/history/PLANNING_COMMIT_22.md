# Commit 22 – Anonymous CV storage (Profile Context)

**Branch:** `feature/commit-22-profile-cv-storage`
**Status:** 🔄 In implementation (Basic implemented)
**Created:** 2026-03-10

---

> **Implementation status:** See `docs/history/COMMIT_22_IMPLEMENTATION_GUIDE.md` for the already implemented basic flow and the verified quality gates.

---

## 🎯 Target

Implemented a new Bounded Context `Profile` for anonymous CV storage and restore via URL tokens. Users can save their CV and reuse it later via a secure link, without a user account.

---

## ✅ Scope (MVP)

### Functional
- ✅ Save CV (encrypted, token-based)
- ✅ Load CV via URL token
- ✅ Token generation (URL-safe Base64, cannot be guessed)
- ✅ Encryption with token as secret (MVP compromise)
- ✅ Unlimited validity (no TTL in MVP)
- ✅ UI integration in `/analyze` (save/load buttons)

### Technically
- ✅ New Bounded Context `Profile` (`app/Domains/Profile/`)
- ✅ CQRS: `StoreResumeCommand` + `GetResumeByTokenQuery`
- ✅ Single action controller (`StoreResumeController`, `LoadResumeController`)
- ✅ Repository Pattern (`ProfileRepository`)
- ✅ Migration + Model (`StoredResume`)
- ✅ Immutable DTOs (`StoreResumeDto`, `ResumeTokenDto`)
- ✅ Pest Tests (Feature + Unit + Security)
- ✅ PHPStan Level 9 + Pint compliant

---

## 🚫 Out of Scope (MVP)

### Functional
- ❌ User accounts (coming later)
- ❌ Multiple CVs per user (only 1 CV per token)
- ❌ CV history/history
- ❌ CV editing (re-save only)
- ❌ TTL / Expiration Date (unlimited in MVP)
- ❌ Manual token election (auto-generated only)

### Technically
- ❌ Separate encryption keys per user
- ❌ Key rotation
- ❌ Audit log for access
- ❌ Rate limiting on storage

---

## 🏗️ Architecture decisions

### 1. Token design

**Decision:** URL-safe Base64, 32 bytes random

```php
// Beispiel-Token
$token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
// Ergebnis: "xK8vQmP3nR-_7dY2..." (~43 Zeichen)
```

**Reason:**
- ✅ Unguessable (256 bit entropy)
- ✅ URL safe (no problems with `+`/`/`)
- ✅ Compact (~43 characters)
- ✅ Standard PHP functions

**Security:**
- Token is URL parameter: HTTPS mandatory (production)
- No token reuse possible (every save = new token)
- Token brute force virtually impossible

---

### 2. Encryption (MVP compromise)

**Decision:** Token serves as the basis for the encryption secret in the MVP

```php
$key = hash('sha256', $token, true);
$iv = random_bytes(12);
$cipherText = openssl_encrypt($cvText, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
```

**Reason (MVP):**
- ✅ No separate key management required
- ✅ Tokens must be secure and unguessable anyway
- ✅ Data encrypted at rest
- ✅ Robust, local implementation without additional infrastructure requirements

**⚠️ Technical Debt:**
- ❌ Token loss = data loss (no recovery)
- ❌ No key rotation possible
- ❌ User-based model later needs refactoring

**🔄 Migration to user-based encryption (Post-MVP):**

**IMPORTANT:** This planning is **mandatory** before implementing user accounts!

**Target architecture (Phase 3, ~Commit 35+):**
```
User-Modell mit separatem Encryption Key
├─ User besitzt Master Key (verschlüsselt mit Passwort)
├─ CV verschlüsselt mit Master Key (nicht mit URL-Token)
├─ Token nur für Authentifizierung/Freigabe
└─ Key Rotation möglich
```

**Migration steps (rough):**

1. **Planning (mandatory before commit ~35):**
   - Threat modeling for key management
   - Decision: Key Derivation (PBKDF2/Argon2) vs. HSM
   - Recovery strategy (backup codes? Email reset?)
   - Data migration plan for existing anonymous CVs

2. **Implementation:**
   - New `users` table with `encryption_key_hash`
   - `stored_resumes` Relation to `users` (nullable for migration)
   - Re-encryption job for old token-based CVs
   - Auth middleware for profile routes

3. **Testing:**
   - Backwards compatibility for anonymous CVs
   - Key rotation testing
   - Recovery flow testing

4. **Documentation:**
   - Security audit of the new architecture
   - User communication about changes
   - Migration guide for existing tokens

**Status:** ⏳ **Not yet planned** (comes before user accounts)

---

### 3. Bounded Context `Profile`

**Decision:** New context next to `Analysis`

**Structure:**
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

**Context demarcation:**
- **`Profile`**: CV storage, loading, token management
- **`Analysis`**: Remains unchanged, uses CV text as input
- **Interaction**: `Analysis` does not know `Profile` (decoupling)

---

## 📋 Implementation phases

See full code examples in `COMMIT_PLAN.md` (Commit 22 section).

### Phase 1: Domain structure & data model (~30min)
- Migration `create_stored_resumes_table`
  - Fields: `token`, `encrypted_cv`, `last_accessed_at`, `timestamps`
- Model `StoredResume`
- Create context directory structure

### Phase 2: Domain Logic (CQRS) (~2h)
- DTOs (immutable, readonly)
- Actions (Generate, Encrypt, Decrypt)
- Repository (store, getByToken, touchLastAccessed)
- Command + Handler (Write)
- Query + Handler (Read)

### Phase 3: HTTP layer (~1h)
- Routes: `POST /profile/store`, `GET /profile/load/{token}`
- Single action controller
- FormRequest (validation)

### Phase 4: UI integration (~1h)
- Analyze view: “💾 Save CV” button
- JavaScript: Async POST, copy token link
- Success/Error messages
- CV loading automatically at token URL

### Phase 5: Tests (~1.5h)
- Feature: Save/Load (Happy Path + Errors)
- Unit: Token Gen, Encrypt/Decrypt, Repository
- Security: Token uniqueness, SQL injection, brute force

---

## ✅ Definition of Done (DoD)

### Functional
- [ ] CV can be saved via UI
- [ ] Token is generated and displayed
- [ ] CV can be loaded via token link
- [ ] CV text is entered in Analyze form
- [ ] Error handling for invalid/missing tokens
- [ ] Error handling for decryption errors

### Technically
- [ ] Migration created and executed
- [ ] Model `StoredResume` created
- [ ] Bounded Context `Profile` structured
- [ ] CQRS: Command + Query + Handlers implemented
- [ ] Actions implemented (Generate, Encrypt, Decrypt)
- [ ] Repository implemented
- [ ] Single-action controller implemented
- [ ] Routes registered
- [ ] UI integrated (save/load buttons)

### Tests
- [ ] Feature testing: Save/Load (Happy Path)
- [ ] Feature testing: error scenarios (invalid tokens, CV too short)
- [ ] Unit testing: token generation
- [ ] Unit tests: encryption/decryption
- [ ] Security tests: token uniqueness, SQL injection
- [ ] All tests green (100% pass)

### Quality gates
- [ ] PHPStan Level 9: 0 Errors
- [ ] Pint: Code style compliant
- [ ] Test coverage ≥ 95%
- [ ] Documentation updated (`ARCHITECTURE.md`, `CODING_GUIDELINES.md`)

---

## 📚 Documentation updates

### `docs/ARCHITECTURE.md`
- [ ] Add Bounded Context `Profile`
- [ ] Document CQRS structure
- [ ] Explain context distinction to `Analysis`
- [ ] Migration note for user-based encryption

### `docs/CODING_GUIDELINES.md`
- [ ] Document crypto rules for commit 22
- [ ] Set token format (URL-safe Base64).
- [ ] Clearly highlight MVP trade-offs

### `docs/ai/PROJECT_OVERVIEW.md`
- [ ] Update "NOT in MVP" (CV storage → ✅)
- [ ] Roadmap: Include user-based encryption as a mandatory step

---

## 🐛 Known Limitations (MVP)

###Security
- ⚠️ Token loss = data loss (no recovery)
- ⚠️ No key rotation possible
- ⚠️ Token sharing = full access (no protection)

### Functional
- ⚠️ No TTL (unlimited storage)
- ⚠️ No multi-CV management
- ⚠️ No CV history

### Performance
- ⚠️ No cleanup routine for old CVs (coming later)

---

## 🔄 Future Expansions (Post-MVP)

### Phase 3 (~Commit 35+): User accounts & secure encryption
- **BEFORE implementation:** Detailed planning is mandatory!
- User-based master keys
- Key Derivation (PBKDF2/Argon2)
- Recovery mechanism
- Re-encryption for old anonymous CVs

### Phase 4 (~Commit 40+): CV management
- Multiple CVs per user
- CV versioning
- CV templates
- Export functions

---

## ⏱️ Estimated effort

- **Phase 1 (data model):** ~30min
- **Phase 2 (Domain Logic):** ~2h
- **Phase 3 (HTTP layer):** ~1h
- **Phase 4 (UI integration):** ~1h
- **Phase 5 (tests):** ~1.5h
- **Documentation:** ~30min
- **Total:** ~6.5h

---

**Last updated:** 2026-03-10
**Version:** 1.0 (Detailed planning for Commit 22)