<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_catalog_page_is_available_to_guests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
