<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_auth_user_cannot_access_adding_tour_(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->postJson('api/v1/admin/travels/'.$travel->id.'/tours');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_tour(): void
    {
        $travel = Travel::factory()->create();

        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->first()->id);

        $response = $this->actingAs($user)->postJson('api/v1/admin/travels/'.$travel->id.'/tours');

        $response->assertStatus(403);
    }

    public function test_saves_tour_successfully_with_valid_data(): void
    {
        $travel = Travel::factory()->create();

        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->first()->id);

        $response = $this->actingAs($user)->postJson('api/v1/admin/travels/'.$travel->id.'/tours', [
            'name' => 'Tour name',
            'starting_date' => now()->toDateString(),
            'ending_date' => now()->addDays(1)->toDateString(),
            'price' => 1525.32,
        ]);

        $response->assertStatus(201);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours');

        $response->assertJsonFragment(['name' => 'Tour name']);
    }
}
