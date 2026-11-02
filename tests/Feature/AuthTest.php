<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Totp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_owner_signs_in_with_a_password_then_sets_up_two_factor(): void
    {
        User::create(['email' => 'owner@example.com', 'password' => 'correct-password-2026', 'role' => 'OWNER']);

        $this->post(route('admin.login.store'), [
            'email' => 'owner@example.com',
            'password' => 'correct-password-2026',
        ])->assertRedirect(route('admin.security'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('audit_logs', ['action' => 'admin.password_ok']);
    }

    public function test_a_wrong_password_is_refused_and_audited(): void
    {
        User::create(['email' => 'owner@example.com', 'password' => 'correct-password-2026', 'role' => 'OWNER']);

        $this->post(route('admin.login.store'), [
            'email' => 'owner@example.com',
            'password' => 'wrong-password-2026',
        ])->assertSessionHas('error', 'credentials');

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['action' => 'admin.login_failed']);
    }

    public function test_a_valid_code_completes_sign_in_and_cannot_be_replayed(): void
    {
        $secret = 'JBSWY3DPEHPK3PXPJBSWY3DPEHPK3PXP';
        $user = User::create([
            'email' => 'admin@example.com',
            'password' => 'correct-password-2026',
            'role' => 'ADMIN',
            'totp_secret' => $secret,
            'totp_enabled' => true,
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'correct-password-2026',
        ])->assertRedirect(route('admin.mfa'));

        $step = intdiv(time(), 30);
        $code = Totp::hotp(Totp::base32Decode($secret), $step);

        $this->post(route('admin.mfa.verify'), ['code' => $code])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertSame($step, $user->fresh()->totp_last_step);

        // the same code is refused on the next attempt
        $this->post(route('admin.mfa.verify'), ['code' => $code])
            ->assertSessionHas('error', 'code');
    }
}
