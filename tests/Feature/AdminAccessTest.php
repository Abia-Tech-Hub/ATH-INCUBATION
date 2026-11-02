<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use App\Support\Masking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, bool $mfaEnabled = false): User
    {
        return User::create([
            'email' => strtolower($role).'@example.com',
            'password' => 'correct-password-2026',
            'role' => $role,
            'totp_enabled' => $mfaEnabled,
        ]);
    }

    private function application(): Application
    {
        return Application::create([
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
            'solution' => 'A marketplace that connects farmers with verified buyers.',
            'team_size' => '3 to 4 people',
            'mentor_areas' => ['Finding customers'],
            'goal' => 'Validate the idea with twenty farmers and run a first pilot.',
            'commitment' => 'Yes, for all 6 weeks',
            'consent' => true,
        ]);
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/team')->assertRedirect(route('admin.login'));
    }

    public function test_customers_cannot_enter_the_admin_area(): void
    {
        $this->actingAs($this->user('CUSTOMER'))->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_staff_see_the_dashboard(): void
    {
        $this->actingAs($this->user('STAFF'))->get('/admin')->assertOk()->assertSee('Applications');
    }

    public function test_admins_must_set_up_two_factor_first(): void
    {
        $this->actingAs($this->user('ADMIN'))->get('/admin')->assertRedirect(route('admin.security'));
    }

    public function test_a_two_factor_session_unlocks_the_dashboard(): void
    {
        $admin = $this->user('ADMIN', true);

        $this->actingAs($admin)
            ->withSession(['mfa_passed' => true])
            ->get('/admin')
            ->assertOk();
    }

    public function test_staff_never_receive_full_contact_details(): void
    {
        $this->application();

        $this->actingAs($this->user('STAFF'))
            ->get('/admin')
            ->assertOk()
            ->assertSee(Masking::email('ana@example.com'))
            ->assertSee(Masking::phone('+234 800 123 4567'))
            ->assertDontSee('ana@example.com')
            ->assertDontSee('+234 800 123 4567');
    }

    public function test_admins_receive_full_contact_details(): void
    {
        $this->application();

        $this->actingAs($this->user('ADMIN', true))
            ->withSession(['mfa_passed' => true])
            ->get('/admin')
            ->assertOk()
            ->assertSee('ana@example.com')
            ->assertSee('+234 800 123 4567');
    }

    public function test_only_admins_can_change_an_application_status(): void
    {
        $application = $this->application();

        $this->actingAs($this->user('STAFF'))
            ->post(route('admin.applications.status', $application), ['status' => 'accepted'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertSame('new', $application->fresh()->status);

        $this->actingAs($this->user('ADMIN', true))
            ->withSession(['mfa_passed' => true])
            ->post(route('admin.applications.status', $application), ['status' => 'accepted'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertSame('accepted', $application->fresh()->status);
    }
}
