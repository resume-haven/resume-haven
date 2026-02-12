<?php

declare(strict_types=1);

it('shows the login screen', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Admin Login');
});
