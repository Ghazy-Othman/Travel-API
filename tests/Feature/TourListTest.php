<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_by_travel_slug_returns_correct_tours()
    {

        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        //
        $response->assertStatus(200);

        // Check we have just one tour
        $response->assertJsonCount(1, 'data');

        //
        $response->assertJsonFragment(['id' => $tour->id]);
    }

    public function test_tours_price_is_shown_correctly()
    {

        $travel = Travel::factory()->create();

        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 123.45,
        ]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment(['price' => '123.45']);
    }

    public function test_tours_list_returns_pagination(): void
    {
        $travel = Travel::factory()->create();
        $tours = Tour::factory(16)->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonPath('meta.last_page', 2);
    }

    //
    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();

        $firstTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(2),
        ]);

        $lastTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(4),
        ]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);

        // Check if the first tour is also the first on the returned tours list
        $response->assertJsonPath('data.0.id', $firstTour->id);
        $response->assertJsonPath('data.1.id', $lastTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();

        $firstTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),

        ]);

        $secondTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);

        $lastTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);

        // Check if the returned tours are sorted by price then by starting date
        $response->assertJsonPath('data.0.id', $firstTour->id);
        $response->assertJsonPath('data.1.id', $secondTour->id);
        $response->assertJsonPath('data.2.id', $lastTour->id);
    }

    public function test_tours_list_filters_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours?priceFrom=100');

        $response->assertStatus(200);

        // Check that we got the two tours
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        // ========================================
        // Check we got just the expensive tour
        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours?priceFrom=150');

        $response->assertStatus(200);

        // Check that we get the two tours
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
    }

    public function test_tours_list_returns_validation_errors(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours?sortBy=notnumber', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(422);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours?dateFrom=sdfsd', [
            'Accept' => 'application/json',
        ]);
        $response->assertStatus(422);
    }
}
