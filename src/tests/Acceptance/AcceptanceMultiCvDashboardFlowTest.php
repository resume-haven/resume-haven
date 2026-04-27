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

test('owner loescht aktuellen token und session setzt auf verbleibenden token zurueck', function (): void {
    $user = User::factory()->create();

    $remainingToken = acceptanceDashboardToken('R', 1);
    $currentToken = acceptanceDashboardToken('R', 2);

    storeAcceptanceDashboardResume($user, $remainingToken, 'Owner CV 1 mit Laravel Erfahrung.', Carbon::now()->subMinutes(2));
    storeAcceptanceDashboardResume($user, $currentToken, 'Owner CV 2 mit PHP und Docker Erfahrung.', Carbon::now()->subMinute());

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('analyze', absolute: false));

    $deleteResponse = $this
        ->withSession([
            'resume_tokens' => [$remainingToken, $currentToken],
            'resume_token' => $currentToken,
        ])
        ->delete(route('profile.delete', ['token' => $currentToken]));

    $deleteResponse->assertRedirect(route('profile.index'));
    $deleteResponse->assertSessionHas('success', 'Lebenslauf wurde geloescht.');
    $deleteResponse->assertSessionHas('resume_tokens', [$remainingToken]);
    $deleteResponse->assertSessionHas('resume_token', $remainingToken);
    $this->assertDatabaseMissing('stored_resumes', ['token' => $currentToken]);

    $dashboardResponse = $this
        ->withSession([
            'resume_tokens' => [$remainingToken],
            'resume_token' => $remainingToken,
        ])
        ->get(route('profile.index'));

    $dashboardResponse->assertOk();
    $dashboardResponse->assertSee($remainingToken);
    $dashboardResponse->assertDontSee($currentToken);
    $dashboardResponse->assertSee('Aktueller Token');
});

test('regular user darf fremden cv nicht loeschen und session bleibt unveraendert', function (): void {
    $owner = User::factory()->create();
    $user = User::factory()->create();

    $ownToken = acceptanceDashboardToken('U', 1);
    $foreignToken = acceptanceDashboardToken('F', 2);

    storeAcceptanceDashboardResume($user, $ownToken, 'Eigenes CV des Users.', Carbon::now()->subMinutes(2));
    storeAcceptanceDashboardResume($owner, $foreignToken, 'Fremdes CV fuer AuthZ-Test.', Carbon::now()->subMinute());

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('analyze', absolute: false));

    $deleteResponse = $this
        ->withSession([
            'resume_tokens' => [$ownToken, $foreignToken],
            'resume_token' => $foreignToken,
        ])
        ->delete(route('profile.delete', ['token' => $foreignToken]));

    $deleteResponse->assertForbidden();
    $deleteResponse->assertSessionHas('resume_tokens', [$ownToken, $foreignToken]);
    $deleteResponse->assertSessionHas('resume_token', $foreignToken);
    $this->assertDatabaseHas('stored_resumes', ['token' => $foreignToken]);
});

test('admin darf fremden cv loeschen und session current-token faellt auf verbleibenden token zurueck', function (): void {
    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $adminToken = acceptanceDashboardToken('A', 9);
    $foreignToken = acceptanceDashboardToken('Z', 9);

    storeAcceptanceDashboardResume($admin, $adminToken, 'Eigenes Admin-CV.', Carbon::now()->subMinutes(2));
    storeAcceptanceDashboardResume($owner, $foreignToken, 'Fremdes CV fuer Admin-Delete.', Carbon::now()->subMinute());

    $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('analyze', absolute: false));

    $deleteResponse = $this
        ->withSession([
            'resume_tokens' => [$adminToken, $foreignToken],
            'resume_token' => $foreignToken,
        ])
        ->delete(route('profile.delete', ['token' => $foreignToken]));

    $deleteResponse->assertRedirect(route('profile.index'));
    $deleteResponse->assertSessionHas('resume_tokens', [$adminToken]);
    $deleteResponse->assertSessionHas('resume_token', $adminToken);
    $this->assertDatabaseMissing('stored_resumes', ['token' => $foreignToken]);
    $this->assertDatabaseHas('stored_resumes', ['token' => $adminToken]);
});
