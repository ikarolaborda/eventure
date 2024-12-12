<?php

namespace Tests\Feature\Api\v1\Reservation;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class ReservationStoreTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/api/v1/events/{eventId}/reserve';

    public function test_unauthenticated_user_cannot_reserve()
    {
        // Create event manually
        $event = Event::create([
            'user_id'          => User::create([
                'name' => 'Event Owner',
                'email' => 'owner@example.com',
                'password' => bcrypt('password123'),
            ])->id,
            'title'            => 'Sample Event',
            'description'      => 'A cool event',
            'start_date'       => now()->addDays(2),
            'end_date'         => now()->addDays(3),
            'booking_deadline' => now()->addDay(),
            'attendee_limit'   => 50,
            'price'            => 20.00,
            'location'         => 'Somewhere',
        ]);

        $response = $this->postJson(str_replace('{eventId}', $event->id, $this->route));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED); // 401
    }

    public function test_user_cannot_reserve_non_existing_event()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        $invalidEventId = 9999;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(str_replace('{eventId}', $invalidEventId, $this->route));

        $response->assertStatus(Response::HTTP_NOT_FOUND) // 404
        ->assertJson(['error' => 'Event not found']);
    }

    public function test_user_cannot_reserve_after_booking_deadline()
    {
        $user = User::create([
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        // Create an event where booking_deadline is in the past
        $event = Event::create([
            'user_id'          => User::create([
                'name' => 'Event Owner',
                'email' => 'owner2@example.com',
                'password' => bcrypt('password123'),
            ])->id,
            'title'            => 'Past Deadline Event',
            'description'      => 'This is past deadline',
            'start_date'       => now()->addDays(2),
            'end_date'         => now()->addDays(3),
            'booking_deadline' => now()->subHour(),
            'attendee_limit'   => 50,
            'price'            => 25.00,
            'location'         => 'Somewhere else',
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(str_replace('{eventId}', $event->id, $this->route));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
        ->assertJson(['error' => 'Booking deadline has passed']);
    }

    public function test_user_cannot_reserve_fully_booked_event()
    {
        $user = User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        // Create an event with attendee_limit = 1
        $event = Event::create([
            'user_id'          => User::create([
                'name' => 'Event Owner',
                'email' => 'owner3@example.com',
                'password' => bcrypt('password123'),
            ])->id,
            'title'            => 'Almost Full Event',
            'description'      => 'Limited seats',
            'start_date'       => now()->addDays(2),
            'end_date'         => now()->addDays(3),
            'booking_deadline' => now()->addDay(),
            'attendee_limit'   => 1,
            'price'            => 30.00,
            'location'         => 'Venue',
        ]);

        // Create one reservation to fully book it
        Reservation::create([
            'user_id'  => User::create([
                'name' => 'Attendee',
                'email' => 'attendee@example.com',
                'password' => bcrypt('password123'),
            ])->id,
            'event_id' => $event->id,
        ]);

        // Now try to reserve with the new user
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(str_replace('{eventId}', $event->id, $this->route));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
        ->assertJson(['error' => 'Event is fully booked']);
    }

    public function test_user_cannot_reserve_event_twice()
    {
        $user = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        $event = Event::create([
            'user_id'          => User::create([
                'name' => 'Event Owner',
                'email' => 'owner4@example.com',
                'password' => bcrypt('password123'),
            ])->id,
            'title'            => 'Multi Reserve Event',
            'description'      => 'You can only reserve once',
            'start_date'       => now()->addDays(2),
            'end_date'         => now()->addDays(3),
            'booking_deadline' => now()->addHours(5),
            'attendee_limit'   => 10,
            'price'            => 10.00,
            'location'         => 'Another Place',
        ]);

        // Create a reservation for this user
        Reservation::create([
            'user_id'  => $user->id,
            'event_id' => $event->id,
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(str_replace('{eventId}', $event->id, $this->route));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) // 422
        ->assertJson(['error' => 'You have already reserved a ticket for this event']);
    }

    public function test_user_can_successfully_reserve_event()
    {
        $user = User::create([
            'name' => 'Clark Kent',
            'email' => 'clark@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = JWTAuth::fromUser($user);

        $eventOwner = User::create([
            'name' => 'Event Owner',
            'email' => 'owner5@example.com',
            'password' => bcrypt('password123'),
        ]);

        $event = Event::create([
            'user_id'          => $eventOwner->id,
            'title'            => 'Open Event',
            'description'      => 'Seats available',
            'start_date'       => now()->addDays(2),
            'end_date'         => now()->addDays(3),
            'booking_deadline' => now()->addHour(),
            'attendee_limit'   => 50,
            'price'            => 15.00,
            'location'         => 'Open Venue',
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(str_replace('{eventId}', $event->id, $this->route));

        $response->assertStatus(Response::HTTP_CREATED) // 201
        ->assertJson(['message' => 'Reservation successful']);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
    }
}
