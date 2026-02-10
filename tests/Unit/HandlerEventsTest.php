<?php

declare(strict_types=1);

use App\Application\Commands\CreateResumeCommand;
use App\Application\Commands\CreateUserCommand;
use App\Application\Commands\UpdateResumeCommand;
use App\Application\Commands\UpdateUserCommand;
use App\Application\Handlers\CreateResumeHandler;
use App\Application\Handlers\CreateUserHandler;
use App\Application\Handlers\UpdateResumeHandler;
use App\Application\Handlers\UpdateUserHandler;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\Entities\User;
use App\Domain\Events\ResumeCreatedEvent;
use App\Domain\Events\ResumeUpdatedEvent;
use App\Domain\Events\UserCreatedEvent;
use App\Domain\Events\UserUpdatedEvent;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\PasswordHash;
use App\Domain\ValueObjects\ResumeId;
use App\Domain\ValueObjects\UserId;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class);

final class FakeResumeRepository implements ResumeRepositoryInterface
{
    public ?Resume $saved = null;
    private ?Resume $existing;

    public function __construct(?Resume $existing = null)
    {
        $this->existing = $existing;
    }

    public function findById(int $id): ?Resume
    {
        if ($this->existing === null || $this->existing->id->value !== $id) {
            return null;
        }

        return $this->existing;
    }

    public function save(object $entity): void
    {
        if (!$entity instanceof Resume) {
            throw new InvalidArgumentException('Expected Resume entity.');
        }

        $this->saved = $entity;
        $this->existing = $entity;
        $entity->id = new ResumeId(1);
    }

    public function delete(int $id): void
    {
    }
}

final class FakeUserRepository implements UserRepositoryInterface
{
    public ?User $saved = null;
    private ?User $existing;

    public function __construct(?User $existing = null)
    {
        $this->existing = $existing;
    }

    public function findById(int $id): ?User
    {
        if ($this->existing === null || $this->existing->id->value !== $id) {
            return null;
        }

        return $this->existing;
    }

    public function save(object $entity): void
    {
        if (!$entity instanceof User) {
            throw new InvalidArgumentException('Expected User entity.');
        }

        $this->saved = $entity;
        $this->existing = $entity;
        $entity->id = new UserId(1);
    }

    public function delete(int $id): void
    {
    }
}


it('dispatches resume created event in handler', function () {
    Event::fake();

    $handler = new CreateResumeHandler(new FakeResumeRepository());
    $command = new CreateResumeCommand('Test Resume', new Email('resume@example.com'));

    $resume = $handler->handle($command);

    Event::assertDispatched(ResumeCreatedEvent::class, function (ResumeCreatedEvent $event) use ($resume) {
        return $event->resume === $resume;
    });
});

it('dispatches user created event in handler', function () {
    Event::fake();

    $handler = new CreateUserHandler(new FakeUserRepository());
    $command = new CreateUserCommand('Test User', new Email('user@example.com'), 'hashed');

    $user = $handler->handle($command);

    Event::assertDispatched(UserCreatedEvent::class, function (UserCreatedEvent $event) use ($user) {
        return $event->user === $user;
    });
});

it('dispatches resume updated event in handler', function () {
    Event::fake();

    $existing = new Resume(new ResumeId(5), new Name('Old Resume'), new Email('old@example.com'));
    $handler = new UpdateResumeHandler(new FakeResumeRepository($existing));
    $command = new UpdateResumeCommand(5, 'New Resume', new Email('new@example.com'));

    $resume = $handler->handle($command);

    Event::assertDispatched(ResumeUpdatedEvent::class, function (ResumeUpdatedEvent $event) use ($resume) {
        return $event->resume === $resume;
    });
});

it('dispatches user updated event in handler', function () {
    Event::fake();

    $existing = new User(
        new UserId(7),
        new Name('Old User'),
        new Email('old@example.com'),
        new PasswordHash('hashed')
    );
    $handler = new UpdateUserHandler(new FakeUserRepository($existing));
    $command = new UpdateUserCommand(7, 'New User', new Email('new@example.com'), 'newhashed');

    $user = $handler->handle($command);

    Event::assertDispatched(UserUpdatedEvent::class, function (UserUpdatedEvent $event) use ($user) {
        return $event->user === $user;
    });
});
