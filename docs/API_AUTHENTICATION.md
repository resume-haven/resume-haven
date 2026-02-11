# API Authentication

This project uses [Laravel Sanctum](https://laravel.com/docs/sanctum) for API authentication. Sanctum provides token-based authentication for APIs while maintaining stateful authentication for traditional web applications.

## Overview

- **Stateful Authentication**: Web browsers can authenticate using sessions (Fortify)
- **Token-Based Authentication**: External clients (mobile apps, CLI, etc.) can authenticate using bearer tokens

## Configuration

### Auth Guard

The `api` guard is configured in `config/auth.php`:

```php
'api' => [
    'driver' => 'sanctum',
    'provider' => 'users',
],
```

### Sanctum Guard

Configured in `config/sanctum.php`:

```php
'guard' => ['web', 'api'],
```

This allows both web sessions and API tokens for authentication.

## API Endpoints

### Public Endpoints (No Authentication)

**Resumes:**
```bash
GET /api/resumes/{id}                      # Get resume details
GET /api/resumes/{id}/status-history       # Get resume status history
```

**Users & Authentication:**
```bash
GET /api/users/{id}                        # Get user details
POST /api/users                            # Create user (registration)
POST /api/tokens                           # Generate API token
POST /api/email/verification-notification  # Resend verification email
```

### Protected Endpoints (Token Authentication + Verified Email Required)

All write operations require authentication via `auth:sanctum` and a verified email:

**Resumes:**
```bash
POST /api/resumes                          # Create resume
PUT /api/resumes/{id}                      # Update resume
PATCH /api/resumes/{id}                    # Partially update resume
DELETE /api/resumes/{id}                   # Delete resume
```

Resume updates and deletes are restricted to the owner or admin users.

**Users:**
```bash
PUT /api/users/{id}                        # Update user
PATCH /api/users/{id}                      # Partially update user
DELETE /api/users/{id}                     # Delete user
```

User updates and deletes are restricted to the account owner or admin users.

**Tokens:**
```bash
POST /api/tokens/revoke                    # Revoke all user tokens
```

**Verification:**
```bash
POST /api/email/verification-notification  # Resend verification email
```

## Getting an API Token

### Via API Endpoint (Recommended)

To obtain a token, send user credentials to the token endpoint:

Note: The email address must be verified.

```bash
curl -X POST http://localhost/api/tokens \
   -H "Content-Type: application/json" \
   -d '{
     "email": "user@example.com",
     "password": "password123",
     "device_name": "My Mobile App"
   }'

# Response (201):
# {
#   "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890",
#   "user": {
#     "id": 1,
#     "name": "John Doe",
#     "email": "user@example.com"
#   }
# }
```

Or with HTTPie:

```bash
http POST http://localhost/api/tokens \
   email="user@example.com" \
   password="password123" \
   device_name="My Mobile App"
```

**Required fields:**
- `email` - User email address
- `password` - User password (minimum 8 characters)
- `device_name` - Label for the token (e.g., "iOS App", "Web Client")

### Creating a Token Programmatically

For server-side testing or administrative purposes:

```php
$user = UserModel::find(1);
$token = $user->createToken('api-token')->plainTextToken;

// Example output:
// "1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890"
```

### Using a Token

Include the token in the `Authorization` header as a Bearer token:

```bash
curl -X POST http://localhost/api/resumes \
   -H "Authorization: Bearer 1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890" \
   -H "Content-Type: application/json" \
   -d '{"name":"My Resume","email":"user@example.com"}'
```

Or with HTTPie:

```bash
http POST http://localhost/api/resumes \
   Authorization:"Bearer 1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890" \
   name="My Resume" \
   email="user@example.com"
```

### Revoking Tokens

Revoke all tokens for the authenticated user:

```bash
curl -X POST http://localhost/api/tokens/revoke \
   -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

Response (200):
```json
{
  "message": "All tokens revoked."
}
```

```php
$user->tokens()->delete();
```

Or a specific token:

```php
$user->tokens()->where('name', 'api-token')->delete();
```

## Middleware

### `auth:sanctum`

Applied to all write operations (POST, PUT, PATCH, DELETE):

```php
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/resumes', [ResumeController::class, 'store']);
    // ...
});
```

When a route is protected:
- Requests WITHOUT a valid token receive a **401 Unauthorized** response
- Requests WITH a valid token proceed to the controller
- The authenticated user is available via `Auth::user()`

## Example Workflow

### 1. Create a User

```bash
curl -X POST http://localhost/api/users \
   -H "Content-Type: application/json" \
   -d '{
     "name": "John Doe",
     "email": "john@example.com",
     "password": "password123"
   }'

# Response (201):
# {
#   "id": 1,
#   "name": "John Doe",
#   "email": "john@example.com",
#   "created_at": "2026-02-10T12:00:00.000Z"
# }

# A verification email is sent after registration.
```

### 2. Generate an API Token

```bash
curl -X POST http://localhost/api/tokens \
   -H "Content-Type: application/json" \
   -d '{
     "email": "john@example.com",
     "password": "password123",
     "device_name": "My App"
   }'

# Response (201):
# {
#   "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890",
#   "user": {
#     "id": 1,
#     "name": "John Doe",
#     "email": "john@example.com"
#   }
# }
```

### 3. Verify Email Address

Before calling protected endpoints, the user must verify their email address
using the link sent during registration.

If you need to resend the verification email:

```bash
curl -X POST http://localhost/api/email/verification-notification \
   -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Response (202):
# {
#   "message": "Verification email sent."
# }
```

During development, verification emails are captured by Mailpit:
- Web UI: http://localhost:8025
- SMTP: mailpit:1025

### 4. Create a Resume with Token

```bash
curl -X POST http://localhost/api/resumes \
   -H "Authorization: Bearer 1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890" \
   -H "Content-Type: application/json" \
   -d '{
     "name": "My First Resume",
     "email": "john@example.com"
   }'

# Response (201):
# {
#   "id": 1,
#   "name": "My First Resume",
#   "email": "john@example.com",
#   "status": "draft"
# }
```

### 4. Update a Resume (Protected)

```bash
curl -X PUT http://localhost/api/resumes/1 \
   -H "Authorization: Bearer 1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890" \
   -H "Content-Type: application/json" \
   -d '{"name": "Updated Resume"}'
```

## User Isolation

The authenticated user can be accessed in controllers:

```php
public function store(CreateResumeRequest $request): JsonResponse
{
    $user = Auth::user();  // Current authenticated user
    // ...
}
```

This ensures users can only operate on their own resources:

```php
if ($resume->user_id !== Auth::id()) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
```

## Token Database Schema

Sanctum stores tokens in the `personal_access_tokens` table:

| Column | Type | Notes |
|--------|------|-------|
| `id` | BIGINT | Primary key |
| `tokenable_id` | BIGINT | References the user |
| `tokenable_type` | VARCHAR | Always `App\Infrastructure\Persistence\UserModel` |
| `name` | VARCHAR | Token name (e.g., 'api-token') |
| `token` | VARCHAR (hashed) | Hashed token value |
| `abilities` | JSON | Token permissions (default: `['*']`) |
| `last_used_at` | TIMESTAMP | Last usage time |
| `created_at` | TIMESTAMP | Creation timestamp |
| `updated_at` | TIMESTAMP | Update timestamp |

Token values are hashed in the database. Only the plaintext token (shown once during creation) can authenticate requests.

## Testing

Run the test suite to verify API routes and authentication:

```bash
make test
```

Tests verify:
- Public endpoints are accessible without tokens
- Protected endpoints reject requests without valid tokens
- Protected endpoints accept properly authenticated requests
- User isolation (authenticated user can only access own data)

## References

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [API Routes](routes/api.php)
- [Auth Configuration](config/auth.php)
- [Sanctum Configuration](config/sanctum.php)
- [Authentication Middleware](app/Http/Middleware/Authenticate.php)
