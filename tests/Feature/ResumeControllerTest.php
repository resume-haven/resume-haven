<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\ResumeModel;

it('shows a resume', function () {
    $resume = ResumeModel::factory()->create();

    $this->getJson("/api/resumes/{$resume->id}")
        ->assertOk()
        ->assertJson([
            'id' => $resume->id,
            'name' => $resume->name,
            'email' => $resume->email,
        ]);
});

it('returns not found for missing resume', function () {
    $this->getJson('/api/resumes/999999')
        ->assertNotFound()
        ->assertJson([
            'message' => 'Resume not found.',
        ]);
});

it('creates a resume', function () {
    $payload = [
        'name' => 'Test Resume',
        'email' => 'resume@example.com',
    ];

    $this->postJson('/api/resumes', $payload)
        ->assertCreated()
        ->assertJson([
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);

    $this->assertDatabaseHas('resumes', [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);
});

it('validates resume creation input', function () {
    $this->postJson('/api/resumes', [
        'name' => '',
        'email' => 'invalid-email',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});
