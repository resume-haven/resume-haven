<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\EncryptResumeAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function storeResumeForDeleteTest(User $user, string $token, string $cvText): void
{
    $encrypted = app(EncryptResumeAction::class)->execute($cvText, $token);

    DB::table('stored_resumes')->insert([
        'token' => $token,
        'user_id' => $user->id,
        'encrypted_cv' => $encrypted,
        'created_at' => now(),
        'updated_at' => now(),
        'last_accessed_at' => null,
    ]);
}

it('redirects guests when trying to delete a stored resume', function (): void {
    $user = User::factory()->create();
    $token = str_pad('DEL-1', 32, 'D');

    storeResumeForDeleteTest($user, $token, 'CV fuer Gast-Delete-Test.');

    $response = $this->delete(route('profile.delete', ['token' => $token]));

    $response->assertRedirect(route('login'));
});

it('deletes own resume and cleans session token keys', function (): void {
    $user = User::factory()->create();
    $token = str_pad('OWN-1', 32, 'O');

    storeResumeForDeleteTest($user, $token, 'CV fuer Owner-Delete-Test.');

    $response = $this
        ->actingAs($user)
        ->withSession([
            'resume_tokens' => [$token],
            'resume_token' => $token,
        ])
        ->delete(route('profile.delete', ['token' => $token]));

    $response->assertRedirect(route('profile.index'));
    $response->assertSessionHas('success', 'Lebenslauf wurde geloescht.');
    $response->assertSessionMissing('resume_tokens');
    $response->assertSessionMissing('resume_token');
    $this->assertDatabaseMissing('stored_resumes', ['token' => $token]);
});

it('forbids deleting foreign resumes for regular users and keeps session tokens', function (): void {
    $owner = User::factory()->create();
    $foreignToken = str_pad('FOR-1', 32, 'F');

    storeResumeForDeleteTest($owner, $foreignToken, 'Fremder CV fuer AuthZ-Test.');

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withSession([
            'resume_tokens' => [$foreignToken],
            'resume_token' => $foreignToken,
        ])
        ->delete(route('profile.delete', ['token' => $foreignToken]));

    $response->assertForbidden();
    $response->assertSessionHas('resume_tokens', [$foreignToken]);
    $response->assertSessionHas('resume_token', $foreignToken);
    $this->assertDatabaseHas('stored_resumes', ['token' => $foreignToken]);
});

it('allows admins to delete foreign resumes', function (): void {
    $owner = User::factory()->create();
    $foreignToken = str_pad('ADM-1', 32, 'A');

    storeResumeForDeleteTest($owner, $foreignToken, 'Fremder CV fuer Admin-Delete-Test.');

    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->withSession([
            'resume_tokens' => [$foreignToken],
            'resume_token' => $foreignToken,
        ])
        ->delete(route('profile.delete', ['token' => $foreignToken]));

    $response->assertRedirect(route('profile.index'));
    $response->assertSessionMissing('resume_tokens');
    $response->assertSessionMissing('resume_token');
    $this->assertDatabaseMissing('stored_resumes', ['token' => $foreignToken]);
});

it('returns not found when deleting an unknown token', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.delete', ['token' => str_pad('MISSING', 32, 'X')]));

    $response->assertNotFound();
});
