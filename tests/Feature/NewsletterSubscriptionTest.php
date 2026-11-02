<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_it_stores_a_normalized_email_address(): void
    {
        $this->from('/')->post(route('newsletter.store'), ['email' => 'NEWS@example.com'])
            ->assertRedirect('/')
            ->assertSessionHas('newsletter_subscribed', true);

        $this->assertDatabaseHas('subscribers', ['email' => 'news@example.com']);
    }

    public function test_it_does_not_create_duplicate_subscriptions(): void
    {
        Subscriber::create(['email' => 'news@example.com']);

        $this->from('/')->post(route('newsletter.store'), ['email' => 'NEWS@example.com'])
            ->assertRedirect('/');

        $this->assertDatabaseCount('subscribers', 1);
    }

    public function test_it_rejects_invalid_email_addresses(): void
    {
        $this->from('/')->post(route('newsletter.store'), ['email' => 'not-an-email'])
            ->assertRedirect('/')
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('subscribers', 0);
    }
}
