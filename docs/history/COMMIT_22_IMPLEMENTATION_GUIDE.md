# Commit 22 - Implementation Guide

**Status:** 🔄 Basic implemented and verified
**Branch:** `feature/commit-22-profile-cv-storage`

---

##Goal

Commit 22 introduces a new Bounded Context `Profile`, which allows a resume to be saved anonymously and reloaded later via an inguessable token link.

---

## Implemented architecture

###Domain
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

### HTTP layer
- `app/Http/Requests/StoreResumeRequest.php`
- `app/Http/Controllers/StoreResumeController.php`
- `app/Http/Controllers/LoadResumeController.php`
- Routes in `routes/web.php`

###UI
- Expansion of `resources/views/analyze.blade.php`
- Save CV via POST to `profile.store`
- Load CV via token link to `profile.load`
- Session-based success/error messages

---

## Technical decisions

###Tokens
- 32 random bytes via `random_bytes()`
- URL-safe Base64 (`+`/`/` -> `-`/`_`, without padding)
- Length typically ~43 characters

### Encryption
- AES-256-GCM
- Key is derived for the MVP from the token via `hash('sha256', $token, true)`
- Payload stores `iv`, `tag` and `cipher` Base64 encoded
- Defective or invalid payloads are safely treated as `null`

### Context boundary
- `Profile` does not know `Analysis` directly
- Integration only takes place via UI/DTO flow: loaded CV text is entered into the analysis form

---

## Current user flow

### Save CV
1. User enters CV text in `analyze.blade.php`
2. POST to `route('profile.store')`
3. `StoreResumeRequest` validates `cv_text`
4. `StoreResumeController` dispatches `StoreResumeCommand`
5. `StoreResumeHandler` generates tokens, encrypts the CV and saves it
6. Redirect to `route('analyze')` with `resume_token`, `resume_link`, `success`

### Load CV
1. User opens `/profile/load/{token}` or enters tokens on the Analyze page
2. `LoadResumeController` validates token format
3. Dispatch from `GetResumeByTokenQuery`
4. `GetResumeByTokenHandler` loads and decrypts the CV
5. `last_accessed_at` is updated
6. Redirect to `route('analyze')` with `loaded_cv`, `loaded_token`, `success`

---

## Tests

### Feature testing
- `tests/Feature/ProfileResumeStorageTest.php`
- `tests/Feature/AnalyzeResumeStorageUiTest.php`

### Unit testing
- `tests/Unit/GenerateTokenActionTest.php`
- `tests/Unit/ResumeCryptoActionsTest.php`

### Covered cases
- Saving a CV
- Validation error if CV is too short
- Load via valid token
- Invalid token format error
- Unknown token error
- Error due to broken encrypted payload
- Update of `last_accessed_at`
- Token format and token uniqueness
- Successful encryption/decryption and error case with incorrect token

---

## Quality Gates (last verified)

- `make test-feature` ✅
- `make test-unit` ✅
- `make phpstan` ✅
- `make pint-analyse` ✅

---

## Known MVP limitations

- No copy-to-clipboard convenience for the storage link
- No multiple management of CVs
- No TTL / automatic cleanup for saved CVs
- In the MVP, the token serves as an access token and the basis for the secret

---

## Next sensitive steps

1. Copy-to-Clipboard for the generated save link
2. Documented cleanup strategy for `stored_resumes`
3. Detailed planning for user-based encryption before introducing accounts
4. Optional later: separate `Profile` landing/management flow