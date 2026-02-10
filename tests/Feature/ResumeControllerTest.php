<?php

declare(strict_types=1);

use App\Domain\Events\ResumeCreatedEvent;
use App\Domain\Events\ResumeDeletedEvent;
use App\Domain\Events\ResumeStatusChangedEvent;
use App\Domain\Events\ResumeUpdatedEvent;
use App\Infrastructure\Persistence\ResumeModel;
use App\Infrastructure\Persistence\ResumeStatusHistoryModel;
use Illuminate\Support\Facades\Event;

it('shows a resume', function () {
    $resume = ResumeModel::factory()->create();

    $this->getJson("/api/resumes/{$resume->id}")
        ->assertOk()
        ->assertJson([
            'id' => $resume->id,
            'name' => $resume->name,
            'email' => $resume->email,
            'status' => $resume->status,
        ]);
});

it('returns not found for missing resume', function () {
    $this->getJson('/api/resumes/999999')
        ->assertNotFound()
        ->assertJson([
            'message' => 'Resume not found.',
        ]);
});

it('shows resume status history', function () {
    $resume = ResumeModel::factory()->create([
        'status' => 'draft',
    ]);

    $history = ResumeStatusHistoryModel::query()->create([
        'resume_id' => $resume->id,
        'from_status' => 'draft',
        'to_status' => 'published',
        'changed_at' => new DateTimeImmutable('2026-02-10T12:30:00Z'),
    ]);

    $this->getJson("/api/resumes/{$resume->id}/status-history")
        ->assertOk()
        ->assertJson([
            [
                'id' => $history->id,
                'resume_id' => $resume->id,
                'from_status' => 'draft',
                'to_status' => 'published',
                'changed_at' => $history->changed_at->toISOString(),
            ],
        ]);
});

it('returns not found for missing resume status history', function () {
    $this->getJson('/api/resumes/999999/status-history')
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
            'status' => 'draft',
        ]);

    $this->assertDatabaseHas('resumes', [
        'name' => $payload['name'],
        'email' => $payload['email'],
        'status' => 'draft',
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
            'status' => 'draft',
        ]);

    $this->assertDatabaseHas('resumes', [
        'id' => $resume->id,
        'name' => $payload['name'],
        'email' => $payload['email'],
        'status' => 'draft',
    ]);

    Event::assertDispatched(ResumeUpdatedEvent::class);
});

it('patches a resume name', function () {
    Event::fake();

    $resume = ResumeModel::factory()->create([
        'name' => 'Old Resume',
        'email' => 'old@example.com',
    ]);

    $this->patchJson("/api/resumes/{$resume->id}", [
        'name' => 'Patched Resume',
    ])
        ->assertOk()
        ->assertJson([
            'id' => $resume->id,
            'name' => 'Patched Resume',
            'email' => 'old@example.com',
            'status' => 'draft',
        ]);

    $this->assertDatabaseHas('resumes', [
        'id' => $resume->id,
        'name' => 'Patched Resume',
        'email' => 'old@example.com',
        'status' => 'draft',
    ]);

    Event::assertDispatched(ResumeUpdatedEvent::class);
});

it('validates resume patch input', function () {
    $resume = ResumeModel::factory()->create();

    $this->patchJson("/api/resumes/{$resume->id}", [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['fields']);
});

it('patches a resume status', function () {
    Event::fake();

    $resume = ResumeModel::factory()->create([
        'status' => 'draft',
    ]);

    $this->patchJson("/api/resumes/{$resume->id}", [
        'status' => 'published',
    ])
        ->assertOk()
        ->assertJson([
            'id' => $resume->id,
            'status' => 'published',
        ]);

    $this->assertDatabaseHas('resumes', [
        'id' => $resume->id,
        'status' => 'published',
    ]);

    $this->assertDatabaseHas('resume_status_history', [
        'resume_id' => $resume->id,
        'from_status' => 'draft',
        'to_status' => 'published',
    ]);

    Event::assertDispatched(ResumeUpdatedEvent::class);
    Event::assertDispatched(ResumeStatusChangedEvent::class);
});

it('rejects invalid resume status', function () {
    $resume = ResumeModel::factory()->create();

    $this->patchJson("/api/resumes/{$resume->id}", [
        'status' => 'invalid',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

it('returns not found for resume patch', function () {
    $this->patchJson('/api/resumes/999999', [
        'name' => 'Patched Resume',
    ])
        ->assertNotFound()
        ->assertJson([
            'message' => 'Resume not found.',
        ]);
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
