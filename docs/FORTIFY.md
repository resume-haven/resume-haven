# Fortify Integration

[Laravel Fortify](https://laravel.com/docs/fortify) provides a headless authentication backend for the application. This document describes the Fortify integration in this project.

## Overview

Fortify is installed and configured to provide:

- **User Registration** - `Features::registration()`
- **Password Reset** - `Features::resetPasswords()`
- **Email Verification** - `Features::emailVerification()`
- **Profile Updates** - `Features::updateProfileInformation()`
- **Password Changes** - `Features::updatePasswords()`
- **Two-Factor Authentication (2FA)** - `Features::twoFactorAuthentication()` *(Web only)*

## User Model Integration

The `UserModel` extends `Authenticatable` and includes Fortify-specific columns:

### Traits

```php
use HasApiTokens;      // Laravel Sanctum - for API token generation
use HasFactory;        // Factory support for testing
use MustVerifyEmail;   // Email verification
use Notifiable;        // Email notifications
```

### Database Columns (added by migration)

```sql
-- Two-Factor Authentication
two_factor_secret              TEXT NULL
two_factor_recovery_codes      TEXT NULL
two_factor_confirmed_at        TIMESTAMP NULL
```

### Model Configuration

**Fillable attributes:**
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'two_factor_confirmed_at',
];
```

**Hidden attributes (for serialization):**
```php
protected $hidden = [
    'password',
    'two_factor_secret',           // Never expose secrets in API
    'two_factor_recovery_codes',   // Never expose codes in API
    'remember_token',
];
```

**Casts:**
```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',  // Fortify 2FA field
        'password' => 'hashed',
    ];
}
```

## Email Verification

Email verification is enabled via `Features::emailVerification()`.

Behavior:
- Newly registered users receive a verification email.
- Admin routes require a verified email address and an admin role.
- Protected API routes require a verified email address.

Login flow:
- The root path `/` redirects to `/admin`.
- Guests are redirected to `/login`.
- Unverified users receive 403 on admin routes.
- Verified users without the admin role receive 403 on admin routes.

Admin UI policies:
- `admin.dashboard` for the dashboard.
- `admin.users.view` for the users list.
- `admin.resumes.view` for the resume list.
- `admin.resumes.view-one` for the resume detail.
- `admin.resumes.update` for status updates.
- `admin.resumes.delete` for deletions.

The verification flow relies on the `MustVerifyEmail` contract and trait.

## Fortify Actions

Fortify uses action classes to handle authentication operations. These are stored in `app/Actions/Fortify/`:

- **CreateNewUser** - Validates and creates new users
- **UpdateUserProfileInformation** - Updates name and email
- **UpdateUserPassword** - Changes password with current password verification
- **ResetUserPassword** - Resets password during password reset flow
- **PasswordValidationRules** - Shared password validation rules

All action classes use the `UserModel` from the DDD infrastructure layer and follow strict typing (`declare(strict_types=1)`).

## Configuration

See `config/fortify.php` for full configuration:

```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

### Authentication Guard

Fortify is configured to use the `web` guard with session-based authentication:

```php
'guard' => 'web',
'middleware' => ['web'],
```

This is suitable for web-based UI. For API authentication, see the [API documentation](docs/API.md).

## Two-Factor Authentication

### TOTP-based 2FA

Fortify implements Time-based One-Time Password (TOTP) authentication via the `two_factor_*` columns:

- `two_factor_secret` - Encrypted TOTP secret key
- `two_factor_recovery_codes` - JSON-encoded recovery codes
- `two_factor_confirmed_at` - Timestamp when 2FA was activated

### Usage

When users enable 2FA in the web UI:

1. A TOTP secret is generated
2. Recovery codes are created and displayed
3. User confirms by entering a code from their authenticator app
4. `two_factor_confirmed_at` is set
5. Future logins require a 2FA code

### Important Security Notes

- `two_factor_secret` and `two_factor_recovery_codes` are automatically hidden from API responses
- Never expose these values in JSON responses
- The `hidden` configuration in `UserModel` ensures they're excluded from serialization

## Testing

User creation and authentication tests are in:
- `tests/Feature/UserControllerTest.php` - API endpoint tests
- Tests verify user creation, updates, and edge cases

Run tests:
```bash
make test
```

## Next Steps

- Implement API token authentication (Laravel Sanctum) for API endpoints
- Add rate limiting to authentication endpoints
- Add audit logging for authentication events

## References

- [Laravel Fortify Documentation](https://laravel.com/docs/fortify)
- [TOTP on Wikipedia](https://en.wikipedia.org/wiki/Time-based_one-time_password)
- [Config file](config/fortify.php)
- [Migration](database/migrations/2026_02_10_192743_add_two_factor_columns_to_users_table.php)
