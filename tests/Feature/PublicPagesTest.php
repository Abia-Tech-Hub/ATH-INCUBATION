<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_all_public_pages_render_successfully(): void
    {
        $routes = [
            route('home'),
            route('about'),
            route('who-can-apply'),
            route('program'),
            route('partners'),
            route('faqs'),
            route('apply'),
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertOk();
            $pageContent = strtolower($response->getContent());

            $this->assertStringNotContainsString('equity', $pageContent);
            $this->assertStringNotContainsString('founder-owned', $pageContent);
            $this->assertStringNotContainsString('founder ownership', $pageContent);
        }

        $home = $this->get(route('home'));
        $home->assertSee('fact-index');
        $home->assertSee('live-pill');
        $home->assertSee('impact-section');
        $home->assertSee('data-counter="6"', false);
        $home->assertSee('data-counter="100"', false);
    }
}
