<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_home_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
