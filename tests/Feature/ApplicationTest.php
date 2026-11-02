<?php

namespace Tests\Feature;

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Ana Okoro',
            'email' => 'ana@example.com',
            'phone' => '+234 800 123 4567',
            'institution' => 'Michael Okpara University of Agriculture (MOUAU)',
            'course' => 'Computer Science',
            'year_of_study' => 'Third year',
            'startup_name' => 'FarmLink',
            'sector' => 'Agriculture and food',
            'stage' => 'Building a prototype',
            'problem' => 'Smallholder farmers cannot find reliable buyers for their produce.',
            'solution' => 'A simple marketplace that connects farmers with verified buyers.',
            'team_size' => '3 to 4 people',
            'link' => 'https://farmlink.example',
            'mentor_areas' => ['Validating the idea', 'Finding customers'],
            'goal' => 'Validate the idea with twenty farmers and run a first pilot.',
            'commitment' => 'Yes, for all 6 weeks',
            'heard_about' => 'Social media',
            'consent' => 'yes',
        ], $overrides);
    }

    public function test_it_stores_every_field_of_the_application(): void
    {
        $response = $this->postJson(route('apply.store'), $this->payload());

        $response->assertCreated()->assertJson(['ok' => true]);

        $application = Application::firstOrFail();

        $this->assertSame('Ana Okoro', $application->full_name);
        $this->assertSame('ana@example.com', $application->email);
        $this->assertSame('+234 800 123 4567', $application->phone);
        $this->assertSame('Michael Okpara University of Agriculture (MOUAU)', $application->institution);
        $this->assertSame('Computer Science', $application->course);
        $this->assertSame('Third year', $application->year_of_study);
        $this->assertSame('FarmLink', $application->startup_name);
        $this->assertSame('Agriculture and food', $application->sector);
        $this->assertSame('Building a prototype', $application->stage);
        $this->assertSame('3 to 4 people', $application->team_size);
        $this->assertSame('https://farmlink.example', $application->link);
        $this->assertSame('Yes, for all 6 weeks', $application->commitment);
        $this->assertSame('Social media', $application->heard_about);
        $this->assertSame(['Validating the idea', 'Finding customers'], $application->mentor_areas);
        $this->assertTrue($application->consent);
        $this->assertSame('new', $application->status);
    }

    public function test_it_rejects_incomplete_or_invalid_applications(): void
    {
        $this->postJson(route('apply.store'), $this->payload(['consent' => '']))->assertStatus(422);
        $this->postJson(route('apply.store'), $this->payload(['problem' => 'Too short']))->assertStatus(422);
        $this->postJson(route('apply.store'), $this->payload(['mentor_areas' => []]))->assertStatus(422);
        $this->postJson(route('apply.store'), $this->payload(['mentor_areas' => ['Not a real area']]))->assertStatus(422);
        $this->postJson(route('apply.store'), $this->payload(['phone' => '123']))->assertStatus(422);
        $this->postJson(route('apply.store'), $this->payload(['year_of_study' => 'Nursery']))->assertStatus(422);
        $this->postJson(route('apply.store'), $this->payload(['institution' => 'Unlisted institution']))->assertStatus(422);
        $this->postJson(route('apply.store'), $this->payload(['commitment' => 'Yes, for all 8 weeks']))->assertStatus(422);

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_it_notifies_the_owner_when_configured(): void
    {
        config(['mail.notify_address' => 'owner@example.com']);

        $this->postJson(route('apply.store'), $this->payload())->assertCreated();

        $this->assertDatabaseCount('applications', 1);
    }
}
