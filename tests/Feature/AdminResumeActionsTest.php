<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\ResumeModel;
use App\Infrastructure\Persistence\RoleModel;
use App\Infrastructure\Persistence\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifyCsrfToken;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);
    $this->withoutMiddleware(BaseVerifyCsrfToken::class);
    $this->withSession(['_token' => 'test-token'])
        ->withHeader('X-CSRF-TOKEN', 'test-token');
});

it('blocks non-admin from updating resume status', function () {
    $user = UserModel::factory()->create();
    $resume = ResumeModel::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.resumes.status', $resume->id), ['status' => 'published'])
        ->assertStatus(403);
});

it('allows admin to update resume status', function () {
    $role = RoleModel::factory()->create(['name' => 'admin']);
    $admin = UserModel::factory()->create();
    $admin->roles()->attach($role);
    $resume = ResumeModel::factory()->create(['status' => 'draft']);

    $this->actingAs($admin)
        ->patch(route('admin.resumes.status', $resume->id), ['status' => 'published'])
        ->assertRedirect(route('admin.resumes.show', $resume->id));

    $this->assertDatabaseHas('resumes', [
        'id' => $resume->id,
        'status' => 'published',
    ]);
});

it('allows admin to delete resume', function () {
    $role = RoleModel::factory()->create(['name' => 'admin']);
    $admin = UserModel::factory()->create();
    $admin->roles()->attach($role);
    $resume = ResumeModel::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.resumes.destroy', $resume->id))
        ->assertRedirect(route('admin.resumes.index'));

    $this->assertDatabaseMissing('resumes', [
        'id' => $resume->id,
    ]);
});
