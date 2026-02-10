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

```bash
GET /api/resumes/{id}                      # Get resume details
GET /api/resumes/{id}/status-history       # Get resume status history
GET /api/users/{id}                        # Get user details
```

### Protected Endpoints (Token Authentication Required)

All write operations require authentication via `auth:sanctum` middleware:

**Resumes:**
```bash
POST /api/resumes                          # Create resume
PUT /api/resumes/{id}                      # Update resume
PATCH /api/resumes/{id}                    # Partially update resume
DELETE /api/resumes/{id}                   # Delete resume
```

**Users:**
```bash
POST /api/users                            # Create user
PUT /api/users/{id}                        # Update user
PATCH /api/users/{id}                      # Partially update user
DELETE /api/users/{id}                     # Delete user
```

## Getting an API Token

To authenticate, a user must obtain a personal access token.

### Creating a Token Programmatically

```php
$user = User::find(1);
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

All tokens for a user can be revoked:

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
Route::middleware(['auth:sanctum'])->group(function () {
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
```

### 2. Generate an API Token

Since token generation is not currently exposed via API, it must be done via Artisan command or web interface:

```bash
php artisan tinker

>>> $user = \App\Infrastructure\Persistence\UserModel::find(1)
>>> $token = $user->createToken('api-token')->plainTextToken
```

### 3. Create a Resume with Token

```bash
curl -X POST http://localhost/api/resumes \
   -H "Authorization: Bearer YOUR_TOKEN_HERE" \
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
   -H "Authorization: Bearer YOUR_TOKEN_HERE" \
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
