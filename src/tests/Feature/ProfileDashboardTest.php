<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\EncryptResumeAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow('2026-04-23 12:00:00');
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function dashboardToken(string $prefix, int $index): string
{
    return str_pad($prefix.$index, 32, $prefix);
}

function storeDashboardResume(User $user, string $token, string $cvText, Carbon $updatedAt): void
{
    $encrypted = app(EncryptResumeAction::class)->execute($cvText, $token);

    DB::table('stored_resumes')->insert([
        'token' => $token,
        'user_id' => $user->id,
        'encrypted_cv' => $encrypted,
        'created_at' => $updatedAt,
        'updated_at' => $updatedAt,
        'last_accessed_at' => null,
    ]);
}

it('redirects guests from profile dashboard to login', function (): void {
    $response = $this->get(route('profile.index'));

    $response->assertRedirect(route('login'));
});

it('shows only own resumes and marks the current token', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $currentToken = dashboardToken('U', 11);

    foreach (range(1, 11) as $index) {
        $token = dashboardToken('U', $index);
        storeDashboardResume(
            $user,
            $token,
            'Eigener CV '.$index.' mit Laravel und PHP Erfahrung.',
            Carbon::now()->subMinutes(11 - $index),
        );
    }

    storeDashboardResume(
        $other,
        dashboardToken('F', 1),
        'Fremder CV mit Symfony Erfahrung.',
        Carbon::now()->subMinute(),
    );

    $response = $this
        ->actingAs($user)
        ->withSession([
            'resume_tokens' => [$currentToken],
            'resume_token' => $currentToken,
        ])
        ->get(route('profile.index'));

    $response->assertOk();
    $response->assertSee('Meine Lebensläufe');
    $response->assertSee($currentToken);
    $response->assertSee('Aktueller Token');
    $response->assertDontSee(dashboardToken('F', 1));
    $response->assertDontSee(dashboardToken('U', 1));
    $response->assertSee(dashboardToken('U', 2));
});

it('supports pagination on the profile dashboard', function (): void {
    $user = User::factory()->create();

    foreach (range(1, 11) as $index) {
        $token = dashboardToken('P', $index);
        storeDashboardResume(
            $user,
            $token,
            'Paginated CV '.$index.' mit Docker und Laravel Erfahrung.',
            Carbon::now()->subMinutes(11 - $index),
        );
    }

    $pageOne = $this->actingAs($user)->get(route('profile.index'));
    $pageTwo = $this->actingAs($user)->get(route('profile.index', ['page' => 2]));

    $pageOne->assertOk();
    $pageOne->assertSee(dashboardToken('P', 11));
    $pageOne->assertDontSee(dashboardToken('P', 1));
    $pageOne->assertSee('Seite 1 von 2');

    $pageTwo->assertOk();
    $pageTwo->assertSee(dashboardToken('P', 1));
    $pageTwo->assertDontSee(dashboardToken('P', 11));
    $pageTwo->assertSee('Seite 2 von 2');
});
