# Design Patterns

## Repository Pattern

Abstract data access:

```php
<?php
declare(strict_types=1);

interface ResumeRepositoryInterface
{
    public function findById(string $id): ?Resume;
    public function findAll(): array;
    public function save(Resume $resume): void;
    public function delete(string $id): void;
}

class EloquentResumeRepository implements ResumeRepositoryInterface
{
    // Implementation details
}

interface ResumeReadRepositoryInterface
{
    public function findById(string $id): ?ResumeReadModel;
}

class EloquentResumeReadRepository implements ResumeReadRepositoryInterface
{
    // Read-model mapping for queries
}
```

**Benefits:**
- Switch storage backends easily
- Mock for testing
- Centralized data logic

## Service Layer

Encapsulate business logic:

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

class ResumeCommandService
{
    public function __construct(
        private CreateResumeHandler $handler,
    ) {}

    public function create(string $name, string $email): Resume
    {
        $command = new CreateResumeCommand($name, new Email($email));

        return $this->handler->handle($command);
    }
}

class UserQueryService
{
    public function __construct(
        private UserReadRepositoryInterface $repository,
    ) {}

    public function getById(int $id): ?UserReadModel
    {
        return $this->repository->findById($id);
    }
}

class UserCommandService
{
    public function __construct(
        private CreateUserHandler $handler,
    ) {}

    public function create(string $name, string $email, string $passwordHash): User
    {
        $command = new CreateUserCommand($name, new Email($email), $passwordHash);

        return $this->handler->handle($command);
    }
}
```

**Benefits:**
- Separates business logic from data access
- Reusable across controllers
- Easier to test
- Clear service boundaries

## Factory Pattern

Create complex objects:

```php
<?php
declare(strict_types=1);

class ResumeFactory
{
    public static function fromArray(array $data): Resume
    {
        $resume = new Resume($data['name']);
        
        foreach ($data['sections'] ?? [] as $sectionData) {
            $resume->addSection(
                $sectionData['type'],
                self::createSection($sectionData)
            );
        }
        
        return $resume;
    }
    
    private static function createSection(array $data): Section
    {
        return match ($data['type']) {
            'experience' => new ExperienceSection(...),
            'education' => new EducationSection(...),
            default => throw new \InvalidArgumentException(),
        };
    }
}
```

**Benefits:**
- Centralized object creation
- Consistent initialization
- Complex validation logic

## Value Objects

Immutable data containers:

```php
<?php
declare(strict_types=1);

class Email
{
    public function __construct(
        private string $address,
    ) {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
    }
    
    public function value(): string
    {
        return $this->address;
    }
}

// Usage
$email = new Email('user@example.com');
$validated = true; // Type system guarantees validity
```

## Domain Events

Domain events capture business occurrences and are emitted from handlers:

```php
<?php
declare(strict_types=1);

final readonly class ResumeCreatedEvent
{
    public function __construct(public Resume $resume) {}
}

final readonly class UserCreatedEvent
{
    public function __construct(public User $user) {}
}
```
