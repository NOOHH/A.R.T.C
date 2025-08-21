<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class StudentSidebarSettingsTest extends TestCase
{
    use WithoutMiddleware;

    public function test_sidebar_settings_api_returns_defaults_without_preview()
    {
        $response = $this->get('/smartprep/api/sidebar-settings');
        $response->assertStatus(200)
                 ->assertJson([ 'success' => true ]);
    }

    public function test_sidebar_settings_api_returns_success_with_preview_params()
    {
        $response = $this->get('/smartprep/api/sidebar-settings?preview=true&website=9');
        $response->assertStatus(200)
                 ->assertJson([ 'success' => true ]);
    }
} 