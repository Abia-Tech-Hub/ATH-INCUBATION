<?php

namespace Tests\Feature;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email, bool $mfa = false): User
    {
        return User::create([
            'email' => $email,
            'password' => 'correct-password-2026',
            'role' => $role,
            'totp_enabled' => $mfa,
        ]);
    }

    private function owner(): User
    {
        return $this->user('OWNER', 'owner@example.com', true);
    }

    public function test_only_owners_can_open_the_team_page(): void
    {
        $staff = $this->user('STAFF', 'staff@example.com');

        $this->actingAs($staff)
            ->get(route('admin.team'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error', 'forbidden');

        $this->actingAs($this->owner())
            ->withSession(['mfa_passed' => true])
            ->get(route('admin.team'))
            ->assertOk()
            ->assertSee('Invite someone');
    }

    public function test_an_owner_invites_an_admin(): void
    {
        $this->actingAs($this->owner())
            ->withSession(['mfa_passed' => true])
            ->post(route('admin.team.store'), ['email' => 'new@example.com', 'role' => 'ADMIN'])
            ->assertRedirect(route('admin.team'));

        $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => 'ADMIN']);
        $this->assertDatabaseHas('login_tokens', ['email' => 'new@example.com', 'purpose' => 'invite']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'team.invited']);
    }

    public function test_existing_customers_remain_customers_until_they_redeem_an_invitation(): void
    {
        $customer = $this->user('CUSTOMER', 'member@example.com');

        $this->actingAs($this->owner())
            ->withSession(['mfa_passed' => true])
            ->post(route('admin.team.store'), ['email' => $customer->email, 'role' => 'STAFF'])
            ->assertRedirect(route('admin.team'));

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'role' => 'CUSTOMER',
            'pending_role' => 'STAFF',
        ]);

        $this->actingAs($customer->fresh())
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_customer_promotion_completes_only_after_invitation_redemption(): void
    {
        $customer = $this->user('CUSTOMER', 'member@example.com');
        $customer->forceFill(['pending_role' => 'STAFF'])->save();
        $token = Str::random(64);
        LoginToken::create([
            'token_hash' => hash('sha256', $token),
            'purpose' => 'invite',
            'email' => $customer->email,
            'expires_at' => now()->addHours(48),
        ]);

        $this->post(route('admin.setup.store'), [
            'token' => $token,
            'password' => 'a-longer-password-for-testing',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'role' => 'STAFF',
            'pending_role' => null,
        ]);
    }

    public function test_a_removed_member_loses_their_role_and_password(): void
    {
        $member = $this->user('ADMIN', 'gone@example.com');

        $this->actingAs($this->owner())
            ->withSession(['mfa_passed' => true])
            ->post(route('admin.team.remove', $member))
            ->assertRedirect(route('admin.team'));

        $member->refresh();
        $this->assertSame('CUSTOMER', $member->role);
        $this->assertNull($member->password);
    }

    public function test_an_owner_cannot_remove_themselves(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->withSession(['mfa_passed' => true])
            ->post(route('admin.team.remove', $owner))
            ->assertSessionHas('error', 'self');

        $this->assertSame('OWNER', $owner->fresh()->role);
    }
}
