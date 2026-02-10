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
