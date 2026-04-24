# Detailplanung Commit 29 — Auth, Rollen & Claim-Flow

**Branch:** `feature/commit-29-auth-roles-claim`  
**Status:** Abgeschlossen  
**Erstellt:** 2026-04-21  
**Abgeschlossen:** 2026-04-23  

---

## Ziel

Laravel Breeze (Blade, minimal) als Auth-Fundament einführen. Nur Login,
Registrierung und Logout — kein Dashboard, keine E-Mail-Verifizierung (MVP).
Rollen (`user` / `admin`) auf dem `User`-Model. Bestehende anonyme
Token-CVs werden automatisch einem User zugeordnet (Claim-Flow).

---

## Entscheidungslog (aus Planungs-Session 2026-04-21)

| # | Frage | Entscheidung |
|---|-------|--------------|
| 1 | Claim-CTA Platzierung | Inline in `result.blade.php` |
| 2 | E-Mail-Verifizierung | Für MVP deaktiviert (`MustVerifyEmail` auskommentiert) |
| 3 | Auto-Claim beim Login | Ja — Session-Token wird beim `Login`-Event auf User übertragen |
| 4 | Direkter Claim beim `store` | Ja — wenn User eingeloggt, wird `user_id` sofort gesetzt |
| 5 | Mehrere Token in Session | Für MVP ein Token; Tech-Debt für CV-Verwaltung (Commit 30+) notiert |
| 6 | Geclaimte CVs & Retention | Geclaimte CVs (`user_id IS NOT NULL`) werden von `pruneExpired()` ausgenommen |

---

## Rollen

| Rolle | Beschreibung |
|-------|--------------|
| `user` | Registrierter Nutzer — kann CVs claimen & verwalten |
| `admin` | Plattformverwaltung, User-Übersicht, Retention-Config |

> `guest` ist kein DB-Eintrag — entspricht dem anonymen Zustand (heutiger Default).

---

## Scope

### Schritt 1 — Breeze installieren & auf Minimal-Auth trimmen

- `composer require laravel/breeze --dev`
- `php artisan breeze:install blade`
- Behalten: `login`, `register`, `logout`
- Entfernen: Dashboard-Views/-Routen, Password-Reset-Mail-Views, E-Mail-Verify-Views
- `MustVerifyEmail` bleibt auskommentiert in `app/Models/User.php`

### Schritt 2 — `UserRole`-Enum + `users`-Migration

- `App\Enums\UserRole` mit Cases `user` und `admin`
- Migration `add_role_to_users_table` — `$table->string('role')->default('user')`
- `User`-Model: Enum-Cast auf `UserRole`, Helper `isAdmin(): bool`, `isUser(): bool`
- `UserFactory`: Default-State `user`, neuer `adminState()`

### Schritt 3 — `stored_resumes`-Migration: nullable `user_id` FK

- Migration `add_user_id_to_stored_resumes_table`
- `$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()`
- `StoredResume`-Model: `belongsTo(User::class)`, `user_id` in `$fillable`

### Schritt 4 — `ProfileRepository` erweitern

Neue Methoden in `app/Domains/Profile/Repositories/ProfileRepository.php`:

- `claimByToken(string $token, int $userId): void` — setzt `user_id`
- `getByUser(int $userId): Collection` — alle CVs eines Users (Vorbereitung CV-Verwaltung)
- `store()` erhält optionalen Parameter `?int $userId = null` — setzt `user_id` sofort bei `create()` wenn übergeben
- `pruneExpired()` schließt Datensätze mit `user_id IS NOT NULL` vom Löschen aus

### Schritt 5 — Direkter Claim beim `store` (User eingeloggt)

- `StoreResumeDto` bekommt optionales `?int $userId`
- `StoreResumeController` übergibt `auth()->id()`
- Handler → Repository setzt `user_id` direkt bei `create()`

### Schritt 6 — Auto-Claim beim Login via Event-Listener

- `App\Listeners\AutoClaimResumesListener` lauscht auf `Illuminate\Auth\Events\Login`
- Liest `resume_token` aus Session
- Prüft: `existsByToken` + `user_id === null`
- Ruft `claimByToken()` auf
- Session-Token bleibt erhalten (Load-Flow `/profile/load/{token}` weiterhin gültig)
- Listener in `EventServiceProvider` / `AppServiceProvider` registrieren

### Schritt 7 — Inline Claim-CTA in `result.blade.php`

Zwei State-Blöcke:

```
[Gast + Session-Token vorhanden]
→ CTA: „CV sichern & Konto erstellen" → /register

[Eingeloggt + CV bereits geclaimt]
→ Dezenter Hinweis: „CV deinem Konto zugeordnet ✓"
```

### Schritt 8 — `ProfilePolicy` + Admin-Middleware-Vorbereitung

- `App\Policies\ProfilePolicy`: `view` / `delete` für Owner (`user_id === auth()->id()`) oder `isAdmin()`
- `App\Http\Middleware\EnsureUserIsAdmin` erstellen
- Als Alias `admin` in `bootstrap/app.php` registrieren
- Noch keine Admin-Routes, nur Vorbereitung für Commit 30+

### Schritt 9 — Tech-Debt dokumentieren

- In `COMMIT_PLAN.md`: `resume_token` in Session hält aktuell einen einzelnen Wert
- Erweiterung auf `resume_tokens[]`-Array folgt mit CV-Verwaltung (Commit 30+)

### Schritt 10 — Tests

Pest-Feature-Tests:

- Register / Login / Logout Flow
- Direkter Claim beim `store` (eingeloggt) — `user_id` gesetzt
- Direkter Claim beim `store` (Gast) — `user_id` null
- Auto-Claim beim Login mit Session-Token — `user_id` gesetzt
- Auto-Claim beim Login ohne Session-Token — kein Fehler
- `pruneExpired()` lässt geclaimte CVs (`user_id IS NOT NULL`) unangetastet
- `ProfilePolicy`: Owner ✓ / Fremder ✗ / Admin ✓
- `UserRole`-Enum: Unit-Test für Cases und Helper-Methoden
- `ProfileRepositoryTest` um `claimByToken`- und `getByUser`-Cases erweitern

---

## Nicht-Scope in Commit 29

- ❌ Kein User-Dashboard / CV-Übersichtsseite (Commit 30+)
- ❌ Keine E-Mail-Verifizierung (MVP-Entscheidung)
- ❌ Keine Password-Reset-Mails
- ❌ Kein `remember_tokens[]`-Array (Tech-Debt, Commit 30+)
- ❌ Keine Admin-Views/-Routen (Middleware-Alias nur vorbereitet)
- ❌ Keine User-basierte Verschlüsselung der CVs (separates Refactoring)

---

## Erfolgskriterien

- Registrierung, Login, Logout funktionieren
- Eingeloggter User: CV-Store verknüpft `user_id` direkt
- Gast-User: CV wird beim Login automatisch geclaimt (Session-Token)
- `pruneExpired()` schont geclaimte CVs
- `ProfilePolicy` schützt fremde CVs
- PHPStan Level 9: 0 Errors
- Pint: sauber
- Test-Coverage: ≥ 95%

## Abschlussnotiz

- Die geplanten Auth-, Rollen- und Claim-Flow-Bausteine wurden umgesetzt.
- Offene Restarbeiten betreffen nur UX-Polish (CTA-/Status-Texte, Feinschliff) und wurden bewusst als Roadmap-Follow-up ausgelagert.
- Die technische Folgearbeit startet mit Commit 30 (`resume_tokens[]`, Multi-CV, Dashboard/CRUD).

---

## Verweise

- Planungs-Session: Chat 2026-04-21 (Breeze/Auth/Claim-Flow-Planung)
- Commit-Plan: `COMMIT_PLAN.md`
- Repository: `app/Domains/Profile/Repositories/ProfileRepository.php`
- Bestehende Tests: `tests/Feature/ProfileRepositoryTest.php`
- Tech-Debt Commit 30+: `resume_tokens[]`-Array, CV-Verwaltung, Admin-Views

