<?php

namespace Tests\Feature\Api\v1\Event;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_event()
    {
        $response = $this->postJson('/api/v1/events', [
            'title' => 'Test Event',
            'description' => 'Some description',
            'start_date' => now()->addDays(2)->toDateTimeString(),
            'end_date' => now()->addDays(3)->toDateTimeString(),
            'booking_deadline' => now()->addDay()->toDateTimeString(),
            'attendee_limit' => 50,
            'price' => 20.00,
            'location' => 'Somewhere',

        ]);

        $response->assertStatus(401); // Unauthenticated
    }

    public function test_authenticated_user_can_create_event()
    {
        $user = User::factory()->create();
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $data = [
            'title'            => 'Test Event',
            'description'      => 'Some description',
            'start_date'       => now()->addDays(2)->toDateTimeString(),
            'end_date'         => now()->addDays(3)->toDateTimeString(),
            'booking_deadline' => now()->addDay()->toDateTimeString(),
            'attendee_limit'   => 50,
            'price'            => 20.00,
            'location'         => 'Somewhere',
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/events', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Event']);
    }
}
