<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domains\Auth\Commands\RegisterUserCommand;
use App\Domains\Auth\Dto\RegisterUserDto;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Bus\Dispatcher;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, Dispatcher $dispatcher): RedirectResponse
    {
        /** @var array{name: string, email: string, password: string, password_confirmation: string} $validated */
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /** @var Authenticatable $user */
        $user = $dispatcher->dispatch(new RegisterUserCommand(
            new RegisterUserDto(
                name: $validated['name'],
                email: $validated['email'],
                password: $validated['password'],
                role: UserRole::User,
            )
        ));

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('analyze', absolute: false));
    }
}
