<?php

namespace Tests\Feature;

use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TravelsListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_travels_list_returns_paginated_data_correctly(): void
    {
        Travel::factory(20)->create(['is_public' => true]);

        $response = $this->get('api/v1/travels');

        $response->assertStatus(200);

        // To check if the returned data is 10 by page
        $response->assertJsonCount(10, 'data');

        // To check how many last pages (if 10 per page and 20 record, then should be 2 pages at most)
        $response->assertJsonPath('meta.last_page', 2);
    }

    #[Test]
    public function test_travels_list_show_only_public_records(): void
    {
        $publicTravel = Travel::factory()->create(['is_public' => true]);
        Travel::factory()->create(['is_public' => false]);

        $response = $this->get('api/v1/travels');

        $response->assertStatus(200);

        // To check if the returned data just on travel (cuz we want just public travels)
        $response->assertJsonCount(1, 'data');

        // To check if the returned travel have the same name as the public travel we just created
        $response->assertJsonPath('data.0.name', $publicTravel->name);
    }
}
