<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicantPortalRemovalTest extends TestCase
{
    public function test_applicant_portal_and_sign_in_endpoints_are_removed(): void
    {
        $this->get('/account')->assertNotFound();
        $this->post('/account/link', ['email' => 'ana@example.com'])->assertNotFound();
        $this->get('/auth/verify')->assertNotFound();
        $this->post('/auth/verify', ['token' => str_repeat('a', 64)])->assertNotFound();
    }

    public function test_public_pages_no_longer_link_to_the_applicant_portal(): void
    {
        foreach (['/', '/about', '/who-can-apply', '/program', '/partners', '/faqs', '/apply'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertDontSee('Applicant Portal')
                ->assertDontSee('Applicant Sign-in')
                ->assertDontSee('View application in portal')
                ->assertDontSee('href="/account"', false);
        }
    }
}
