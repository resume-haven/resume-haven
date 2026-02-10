# Core Concepts

## Strict Type Safety

All PHP files use `declare(strict_types=1)`:

```php
<?php
declare(strict_types=1);

namespace App\Domain\Entities;

class Resume
{
    private string $name;
    private array $sections = [];
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

**Benefits:**
- Runtime type checking prevents bugs
- IDE can detect type mismatches
- Self-documenting code
- Easier debugging

## Namespacing

Organize code by responsibility (DDD layers):

```
App\
├── Domain\              # Core business logic
│   ├── Entities\        # Domain entities
│   ├── ValueObjects\    # Immutable value objects
│   ├── Contracts\       # Domain interfaces
│   └── Services\        # Domain services
├── Application\         # Use cases and orchestration
│   ├── Commands\        # Write operations
│   ├── Queries\         # Read operations
│   ├── Handlers\        # CQRS handlers
│   ├── Contracts\       # Read repository interfaces
│   ├── DTOs\            # Data transfer objects
│   ├── ReadModels\      # Query models
│   └── Services\        # Application services
├── Infrastructure\      # External dependencies
│   ├── Persistence\     # Eloquent models
│   ├── Repositories\    # Repository + read repository implementations
│   └── External\        # External API integrations
├── Http\                # UI layer (controllers, middleware)
└── ...
```

## Dependency Injection

Request dependencies explicitly:

```php
<?php
declare(strict_types=1);

class ResumeQueryService
{
    public function __construct(
        private ResumeReadRepositoryInterface $repository,
    ) {}
    
    public function getById(int $id): ?ResumeReadModel
    {
        return $this->repository->findById($id);
    }
}
```
