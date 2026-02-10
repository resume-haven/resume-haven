# Testing Strategy

## Unit Tests

Test individual components in isolation:

```php
<?php
declare(strict_types=1);

class ResumeTest extends TestCase
{
    public function testCanCreateResume(): void
    {
        $resume = new Resume('John Doe');
        
        $this->assertEquals('John Doe', $resume->name());
    }
    
    public function testThrowsOnInvalidName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new Resume('');
    }
}
```

## Integration Tests

Test component interactions:

```php
<?php
declare(strict_types=1);

class ResumeServiceTest extends TestCase
{
    public function testCanBuildCompleteResume(): void
    {
        $repository = new MockResumeReadRepository();
        $service = new ResumeQueryService($repository);

        $result = $service->getById(123);

        $this->assertNotNull($result);
    }
}
```

## Domain Event Tests

Verify that handlers and endpoints dispatch domain events. Unit tests use the
full Laravel TestCase to bootstrap facades:

```php
<?php

use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class);

it('dispatches resume created event', function () {
    Event::fake();

    $handler = new CreateResumeHandler(new FakeResumeRepository());
    $handler->handle(new CreateResumeCommand('Test Resume', new Email('resume@example.com')));

    Event::assertDispatched(ResumeCreatedEvent::class);
});
```

Feature tests assert event dispatch on POST endpoints:

- [tests/Feature/ResumeControllerTest.php](tests/Feature/ResumeControllerTest.php)
- [tests/Feature/UserControllerTest.php](tests/Feature/UserControllerTest.php)
