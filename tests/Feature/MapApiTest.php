<?php

namespace Tests\Feature;

use App\Models\AttendancePoint;
use App\Models\Projek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_active_attendance_points_via_api(): void
    {
        $projek = Projek::factory()->create();
        $point = AttendancePoint::factory()->create([
            'projek_id' => $projek->id,
            'is_active' => true,
        ]);

        $response = $this->getJson(route('api.maps.points'));

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonFragment([
                'id' => $point->id,
                'name' => $point->name,
                'projek_id' => $projek->id,
            ]);
    }

    public function test_can_get_single_attendance_point_via_api(): void
    {
        $point = AttendancePoint::factory()->create();

        $response = $this->getJson(route('api.maps.points.show', $point));

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $point->id,
                    'name' => $point->name,
                ],
            ]);
    }

    public function test_can_validate_location_inside_radius(): void
    {
        $point = AttendancePoint::factory()->create([
            'latitude' => -7.2574719,
            'longitude' => 112.7520883,
            'radius_meters' => 200,
        ]);

        // Very close coordinate (approx 15 meters)
        $response = $this->postJson(route('api.maps.validate'), [
            'attendance_point_id' => $point->id,
            'latitude' => -7.2575000,
            'longitude' => 112.7521000,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'is_within_radius' => true,
            ]);
    }

    public function test_can_validate_location_outside_radius(): void
    {
        $point = AttendancePoint::factory()->create([
            'latitude' => -7.2574719,
            'longitude' => 112.7520883,
            'radius_meters' => 100,
        ]);

        // Coordinates in Jakarta while point is in Surabaya (~660 km away)
        $response = $this->postJson(route('api.maps.validate'), [
            'attendance_point_id' => $point->id,
            'latitude' => -6.2087634,
            'longitude' => 106.845599,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'is_within_radius' => false,
            ]);

        $this->assertGreaterThan(500000, $response->json('distance_meters'));
    }
}

