<?php

declare(strict_types=1);

namespace App\Domain\Events;

use App\Domain\Entities\User;

final readonly class UserDeletedEvent
{
    public function __construct(public User $user)
    {
    }
}
