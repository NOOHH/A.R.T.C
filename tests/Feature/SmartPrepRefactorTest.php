<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartPrepRefactorTest extends TestCase
{
    public function test_platform_health_route_works()
    {
        $response = $this->get('/platform/health');
        
        $response->assertStatus(200)
                 ->assertJson(['status' => 'ok', 'app' => 'SmartPrep Platform - New Architecture']);
    }
    
    public function test_artc_sample_route_works()
    {
        $response = $this->get('/artc-sample');
        
        $response->assertStatus(200)
                 ->assertSeeText('ARTC sample route within Clients\\ARTC namespace');
    }
    
    public function test_legacy_homepage_still_works()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        // Homepage should still load (regardless of content)
    }
}
