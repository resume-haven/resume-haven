# Detailed planning Commit 29 — Auth, roles & claim flow

**Branch:** `feature/commit-29-auth-roles-claim`
**Status:** Completed
**Created:** 2026-04-21
**Completed:** 2026-04-23

---

##Goal

Introduce Laravel Breeze (Blade, minimal) as an auth foundation. Login only,
Registration and logout — no dashboard, no email verification (MVP).
Roles (`user` / `admin`) on the `User` model. Existing anonymous
Token CVs are automatically assigned to a user (claim flow).

---

## Decision log (from planning session 2026-04-21)

| # | Question | decision |
|---|-------|--------------|
| 1 | Claim CTA placement | Inline in `result.blade.php` |
| 2 | Email Verification | Disabled for MVP (`MustVerifyEmail` commented out) |
| 3 | Auto claim upon login | Yes — session token is transferred to user at `Login` event |
| 4 | Direct claim at `store` | Yes — when user is logged in, `user_id` is set immediately |
| 5 | Multiple tokens in Session | For MVP, a token; Tech Debt for CV Management (Commit 30+) listed |
| 6 | Claimed CVs & Retention | Claimed CVs (`user_id IS NOT NULL`) are excluded from `pruneExpired()` |

---

## Roll

| role | Description |
|-------|--------------|
| `user` | Registered user — can claim & manage CVs |
| `admin` | Platform management, user overview, retention config |

> `guest` is not a DB entry — corresponds to the anonymous state (current default).

---

##Scope

### Step 1 — Install Breeze & trim to minimal auth

- `composer require laravel/breeze --dev`
- `php artisan breeze:install blade`
- Keep: `login`, `register`, `logout`
- Remove: Dashboard Views/Routes, Password Reset Mail Views, Email Verify Views
- `MustVerifyEmail` remains commented out in `app/Models/User.php`

### Step 2 — `UserRole` enum + `users` migration

- `App\Enums\UserRole` with cases `user` and `admin`
- Migration `add_role_to_users_table` — `$table->string('role')->default('user')`
- `User` model: Enum cast on `UserRole`, helper `isAdmin(): bool`, `isUser(): bool`
- `UserFactory`: Default state `user`, new `adminState()`

### Step 3 — `stored_resumes` migration: nullable `user_id` FK

- Migration `add_user_id_to_stored_resumes_table`
- `$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()`
- `StoredResume` model: `belongsTo(User::class)`, `user_id` in `$fillable`

### Step 4 — Expand `ProfileRepository`

New methods in `app/Domains/Profile/Repositories/ProfileRepository.php`:

- `claimByToken(string $token, int $userId): void` — sets `user_id`
- `getByUser(int $userId): Collection` — all CVs of a user (preparation for CV management)
- `store()` receives optional parameters `?int $userId = null` — immediately sets `user_id` to `create()` when passed
- `pruneExpired()` excludes records with `user_id IS NOT NULL` from deletion

### Step 5 — Direct claim to `store` (user logged in)

- `StoreResumeDto` gets optional `?int $userId`
- `StoreResumeController` hands over to `auth()->id()`
- Handler → Repository sets `user_id` directly at `create()`

### Step 6 — Auto-claim when logging in via event listener

- `App\Listeners\AutoClaimResumesListener` listens to `Illuminate\Auth\Events\Login`
- Reads `resume_token` from session
- Checks: `existsByToken` + `user_id === null`
- Calls `claimByToken()`
- Session token remains intact (load flow `/profile/load/{token}` still valid)
- Register listeners in `EventServiceProvider` / `AppServiceProvider`

### Step 7 — Inline Claim CTA in `result.blade.php`

Two state blocks:

```
[Gast + Session-Token vorhanden]
→ CTA: „CV sichern & Konto erstellen" → /register

[Eingeloggt + CV bereits geclaimt]
→ Dezenter Hinweis: „CV deinem Konto zugeordnet ✓"
```

### Step 8 — `ProfilePolicy` + Admin middleware preparation

- `App\Policies\ProfilePolicy`: `view` / `delete` for Owner (`user_id === auth()->id()`) or `isAdmin()`
- Create `App\Http\Middleware\EnsureUserIsAdmin`
- Register as alias `admin` in `bootstrap/app.php`
- No admin routes yet, just preparation for commit 30+

### Step 9 — Document tech debt

- In `COMMIT_PLAN.md`: `resume_token` in Session currently holds a single value
- Expansion to `resume_tokens[]` array follows with CV management (Commit 30+)

### Step 10 — Tests

Pest feature tests:

- Register/Login/Logout Flow
- Direct claim at `store` (logged in) — `user_id` set
- Direct claim to `store` (guest) — `user_id` null
- Auto-claim when logging in with session token — `user_id` set
- Auto-claim when logging in without session token — no error
- `pruneExpired()` leaves claimed CVs (`user_id IS NOT NULL`) untouched
- `ProfilePolicy`: Owner ✓ / Stranger ✗ / Admin ✓
- `UserRole` enum: Unit test for cases and helper methods
- Expand `ProfileRepositoryTest` with `claimByToken` and `getByUser` cases

---

## Non scope in commit 29

- ❌ No user dashboard / CV overview page (Commit 30+)
- ❌ No email verification (MVP decision)
- ❌ No password reset emails
- ❌ No `remember_tokens[]` array (tech-debt, commit 30+)
- ❌ No admin views/routes (middleware alias only prepared)
- ❌ No user-based encryption of CVs (separate refactoring)

---

## Success criteria

- Registration, login, logout work
- Logged in user: CV-Store links `user_id` directly
- Guest user: CV is automatically claimed upon login (session token)
- `pruneExpired()` protects claimed CVs
- `ProfilePolicy` protects third-party CVs
- PHPStan Level 9: 0 Errors
- Pint: clean
- Test coverage: ≥ 95%

## Closing note

- The planned auth, role and claim flow modules have been implemented.
- Open remaining work only concerns UX polish (CTA/status texts, fine-tuning) and was deliberately outsourced as roadmap follow-up.
- The technical follow-up work starts with commit 30 (`resume_tokens[]`, Multi-CV, Dashboard/CRUD).

---

## References

- Planning Session: Chat 2026-04-21 (Breeze/Auth/Claim Flow Planning)
- Commit plan: `COMMIT_PLAN.md`
- Repository: `app/Domains/Profile/Repositories/ProfileRepository.php`
- Existing tests: `tests/Feature/ProfileRepositoryTest.php`
- Tech Debt Commit 30+: `resume_tokens[]` array, CV management, admin views