<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test the landing page can be rendered.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('PT Technodrives');
        $response->assertSee('Radar Terminal GPS');
    }

    /**
     * Test login screen is accessible.
     */
    public function test_login_screen_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
