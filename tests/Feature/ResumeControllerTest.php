<?php

declare(strict_types=1);

use App\Domain\Events\ResumeCreatedEvent;
use App\Domain\Events\ResumeDeletedEvent;
use App\Domain\Events\ResumeUpdatedEvent;
use App\Infrastructure\Persistence\ResumeModel;
use Illuminate\Support\Facades\Event;

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
    Event::fake();

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

    Event::assertDispatched(ResumeCreatedEvent::class);
});

it('updates a resume', function () {
    Event::fake();

    $resume = ResumeModel::factory()->create([
        'name' => 'Old Resume',
        'email' => 'old@example.com',
    ]);

    $payload = [
        'name' => 'Updated Resume',
        'email' => 'updated@example.com',
    ];

    $this->putJson("/api/resumes/{$resume->id}", $payload)
        ->assertOk()
        ->assertJson([
            'id' => $resume->id,
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);

    $this->assertDatabaseHas('resumes', [
        'id' => $resume->id,
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);

    Event::assertDispatched(ResumeUpdatedEvent::class);
});

it('deletes a resume', function () {
    Event::fake();

    $resume = ResumeModel::factory()->create();

    $this->deleteJson("/api/resumes/{$resume->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('resumes', [
        'id' => $resume->id,
    ]);

    Event::assertDispatched(ResumeDeletedEvent::class);
});

it('returns not found for resume delete', function () {
    $this->deleteJson('/api/resumes/999999')
        ->assertNotFound()
        ->assertJson([
            'message' => 'Resume not found.',
        ]);
});

it('returns not found for resume update', function () {
    $this->putJson('/api/resumes/999999', [
        'name' => 'Missing Resume',
        'email' => 'missing@example.com',
    ])
        ->assertNotFound()
        ->assertJson([
            'message' => 'Resume not found.',
        ]);
});

it('validates resume update input', function () {
    $resume = ResumeModel::factory()->create();

    $this->putJson("/api/resumes/{$resume->id}", [
        'name' => '',
        'email' => 'invalid-email',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

it('validates resume creation input', function () {
    $this->postJson('/api/resumes', [
        'name' => '',
        'email' => 'invalid-email',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

it('rejects long resume email', function () {
    $this->postJson('/api/resumes', [
        'name' => 'Valid Name',
        'email' => str_repeat('a', 256) . '@example.com',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
