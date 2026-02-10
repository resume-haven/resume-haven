<?php

declare(strict_types=1);

use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\PasswordHash;
use App\Domain\ValueObjects\ResumeId;
use App\Domain\ValueObjects\ResumeStatus;
use App\Domain\ValueObjects\UserId;

it('creates name with trimmed value', function () {
    $name = new Name('  Jane Doe  ');

    expect($name->value)->toBe('Jane Doe');
});

it('rejects empty name', function () {
    new Name('');
})->throws(InvalidArgumentException::class);

it('rejects overly long name', function () {
    new Name(str_repeat('a', 201));
})->throws(InvalidArgumentException::class);

it('accepts valid email', function () {
    $email = new Email('user@example.com');

    expect($email->value)->toBe('user@example.com');
});

it('rejects invalid email', function () {
    new Email('invalid-email');
})->throws(InvalidArgumentException::class);

it('rejects empty password hash', function () {
    new PasswordHash('');
})->throws(InvalidArgumentException::class);

it('rejects overly long password hash', function () {
    new PasswordHash(str_repeat('p', 256));
})->throws(InvalidArgumentException::class);

it('accepts resume id zero or greater', function () {
    expect(new ResumeId(0)->value)->toBe(0);
    expect(new ResumeId(5)->value)->toBe(5);
});

it('rejects negative resume id', function () {
    new ResumeId(-1);
})->throws(InvalidArgumentException::class);

it('accepts valid resume status values', function () {
    expect(new ResumeStatus('draft')->value)->toBe('draft');
    expect(new ResumeStatus('published')->value)->toBe('published');
    expect(new ResumeStatus('archived')->value)->toBe('archived');
});

it('rejects invalid resume status', function () {
    new ResumeStatus('invalid');
})->throws(InvalidArgumentException::class);

it('accepts user id zero or greater', function () {
    expect(new UserId(0)->value)->toBe(0);
    expect(new UserId(9)->value)->toBe(9);
});

it('rejects negative user id', function () {
    new UserId(-1);
})->throws(InvalidArgumentException::class);
