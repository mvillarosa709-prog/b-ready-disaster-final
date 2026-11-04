<?php

use App\Models\Certification;
use App\Models\User;
use App\Models\Workshop;

beforeEach(function () {
    $this->seed();
});

test('trainers and admins can review and approve pending certificates', function () {
    $certificate = Certification::where('certificate_number', 'BRD-2026-TF9284K')->firstOrFail();
    $certificate->update([
        'status' => 'pending',
        'approved_by' => null,
        'approved_at' => null,
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    expect($admin->role)->toBe('admin');

    $this->actingAs($admin)
        ->get(route('trainer.certificates.pending'))
        ->assertOk()
        ->assertSee($certificate->certificate_number)
        ->assertSee('Approve & Sign');

    $trainer = User::where('email', 'trainer@example.com')->firstOrFail();
    $this->actingAs($trainer)
        ->post(route('trainer.certificates.approve', $certificate))
        ->assertRedirect()
        ->assertSessionHas('success', 'Certificate approved and signed.');

    $this->assertDatabaseHas('certifications', [
        'id' => $certificate->id,
        'status' => 'approved',
        'approved_by' => $trainer->id,
    ]);
    expect($certificate->fresh()->approved_at)->not->toBeNull();

    $this->actingAs($certificate->teacher)
        ->get(route('teacher.assessments.certificate', $certificate->workshop))
        ->assertOk()
        ->assertSee('Digitally signed by '.$trainer->name)
        ->assertSee('Approved & Signed');
});

test('teachers cannot access the certificate approval queue', function () {
    $teacher = User::where('email', 'teacher@example.com')->firstOrFail();

    $this->actingAs($teacher)
        ->get(route('trainer.certificates.pending'))
        ->assertForbidden();
});

test('pending certificates display the verification notice instead of a trainer signature', function () {
    $certificate = Certification::where('certificate_number', 'BRD-2026-TF9284K')->firstOrFail();
    $certificate->update([
        'status' => 'pending',
        'approved_by' => null,
        'approved_at' => null,
    ]);
    $workshop = Workshop::findOrFail($certificate->workshop_id);
    $teacher = $certificate->teacher;

    $this->actingAs($teacher)
        ->get(route('teacher.assessments.certificate', $workshop))
        ->assertOk()
        ->assertSee('Naghihintay pa ng beripikasyon at lagda mula sa DRR Trainer.')
        ->assertSee('Pending Verification');
});
