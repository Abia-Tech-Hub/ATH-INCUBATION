<?php

namespace Tests\Feature;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MagicLinkTest extends TestCase
{
    use RefreshDatabase;

    private function issue(string $email, string $purpose = 'signin'): string
    {
        $token = Str::random(64);
        LoginToken::create([
            'token_hash' => hash('sha256', $token),
            'purpose' => $purpose,
            'email' => $email,
            'expires_at' => now()->addMinutes(15),
        ]);

        return $token;
    }

    public function test_guests_see_the_sign_in_link_form(): void
    {
        $this->get('/account')->assertOk()->assertSee('Email me a sign-in link');
    }

    public function test_requesting_a_link_stores_a_single_use_token(): void
    {
        $this->post(route('account.link'), ['email' => 'ana@example.com'])
            ->assertRedirect(route('account'));

        $this->assertDatabaseCount('login_tokens', 1);
        $this->assertSame('ana@example.com', LoginToken::first()->email);
    }

    public function test_a_link_signs_the_customer_in_once(): void
    {
        $token = $this->issue('ana@example.com');

        $this->post(route('auth.verify.store'), ['token' => $token])
            ->assertRedirect(route('account'));

        $this->assertAuthenticated();
        $this->assertNotNull(LoginToken::first()->used_at);
        $this->assertSame('CUSTOMER', User::firstWhere('email', 'ana@example.com')->role);

        auth()->logout();

        $this->post(route('auth.verify.store'), ['token' => $token])
            ->assertRedirect(route('auth.verify'))
            ->assertSessionHas('error', 'expired');
    }

    public function test_team_accounts_cannot_sign_in_by_email_link(): void
    {
        User::create(['email' => 'admin@example.com', 'role' => 'ADMIN', 'password' => 'correct-password-2026']);
        $token = $this->issue('admin@example.com');

        $this->post(route('auth.verify.store'), ['token' => $token])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error', 'use_password');

        $this->assertGuest();
    }
}
