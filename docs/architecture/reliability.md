# Data Flow and Error Handling

## Data Flow

```
HTTP Request
    ↓
Controller (validate input)
    ↓
Service (apply business logic)
    ↓
Repository (data access)
    ↓
Database (store/retrieve data)
    ↓
Mapper (transform to view model)
    ↓
HTTP Response
```

## Error Handling Strategy

### Exception Hierarchy

```php
<?php
declare(strict_types=1);

abstract class ApplicationException extends \Exception {}

// Domain exceptions
class InvalidResumeException extends ApplicationException {}
class ResumeNotFoundException extends ApplicationException {}

// Application exceptions
class ValidationException extends ApplicationException {}
class ExportException extends ApplicationException {}
```

### Error Handling

```php
<?php
declare(strict_types=1);

try {
    $resume = $service->buildResume($data);
} catch (InvalidResumeException $e) {
    // Log and return user-friendly error
    Log::warning('Resume validation failed', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Invalid resume data'], 422);
} catch (ApplicationException $e) {
    // Log application-level errors
    Log::error('Resume build failed', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Internal server error'], 500);
}
```
