<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\EncryptResumeAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow('2026-04-24 09:00:00');
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function acceptanceDashboardToken(string $prefix, int $index): string
{
    return str_pad($prefix.$index, 32, $prefix);
}

function storeAcceptanceDashboardResume(User $user, string $token, string $cvText, Carbon $updatedAt): void
{
    $encryptedResume = app(EncryptResumeAction::class)->execute($cvText, $token);

    DB::table('stored_resumes')->insert([
        'token' => $token,
        'user_id' => $user->id,
        'encrypted_cv' => $encryptedResume,
        'created_at' => $updatedAt,
        'updated_at' => $updatedAt,
        'last_accessed_at' => null,
    ]);
}

test('nutzende sehen im dashboard nur eigene cvs mit pagination und current-token-markierung', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $currentToken = acceptanceDashboardToken('A', 1);

    foreach (range(1, 11) as $index) {
        $token = acceptanceDashboardToken('A', $index);

        storeAcceptanceDashboardResume(
            $user,
            $token,
            'Eigener Multi-CV '.$index.' mit Laravel, PHP, Docker und Testing Erfahrung.',
            Carbon::now()->subMinutes(11 - $index),
        );
    }

    storeAcceptanceDashboardResume(
        $otherUser,
        acceptanceDashboardToken('F', 1),
        'Fremder CV mit Symfony und Vue Erfahrung.',
        Carbon::now()->subMinute(),
    );

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('analyze', absolute: false));

    $pageOne = $this->withSession([
        'resume_tokens' => [$currentToken],
        'resume_token' => $currentToken,
    ])->get(route('profile.index'));

    $pageTwo = $this->withSession([
        'resume_tokens' => [$currentToken],
        'resume_token' => $currentToken,
    ])->get(route('profile.index', ['page' => 2]));

    $pageOne->assertOk();
    $pageOne->assertSee('Meine Lebensläufe');
    $pageOne->assertSee(acceptanceDashboardToken('A', 11));
    $pageOne->assertDontSee($currentToken);
    $pageOne->assertDontSee(acceptanceDashboardToken('F', 1));
    $pageOne->assertSee('Seite 1 von 2');

    $pageTwo->assertOk();
    $pageTwo->assertSee($currentToken);
    $pageTwo->assertSee('Aktueller Token');
    $pageTwo->assertDontSee(acceptanceDashboardToken('A', 11));
    $pageTwo->assertDontSee(acceptanceDashboardToken('F', 1));
    $pageTwo->assertSee('Seite 2 von 2');
});
