<?php

namespace Tests\Feature\Api\v1\Review;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;


/** I deviated from SOLID here given the time constraints. I would have preferred to use a separate test class for each CRUD operation. */
class ReviewCrudTest extends TestCase
{
    use RefreshDatabase;

    protected string $listRoute = '/api/v1/events/{eventId}/reviews';
    protected string $showRoute = '/api/v1/events/{eventId}/reviews/{reviewId}';
    protected string $storeRoute = '/api/v1/events/{eventId}/reviews';
    protected string $updateRoute = '/api/v1/events/{eventId}/reviews/{reviewId}';
    protected string $deleteRoute = '/api/v1/events/{eventId}/reviews/{reviewId}';

    public function test_can_list_reviews_for_event()
    {
        $event = Event::create([
            'user_id' => User::create(['name'=>'Owner','email'=>'owner@example.com','password'=>bcrypt('pass')])->id,
            'title' => 'Test Event',
            'description' => 'Desc',
            'start_date' => now()->subDays(3),
            'end_date' => now()->subDays(2),
            'booking_deadline' => now()->subDays(4),
            'attendee_limit' => 100,
            'price' => 10,
            'location' => 'Somewhere'
        ]);

        Review::create([
            'user_id' => User::create(['name'=>'John','email'=>'john@example.com','password'=>bcrypt('pass')])->id,
            'event_id' => $event->id,
            'rating' => 5,
            'comment' => 'Great event!',
            'is_approved' => false
        ]);

        $response = $this->getJson(str_replace('{eventId}', $event->id, $this->listRoute));
        $response->assertStatus(200)
            ->assertJsonStructure(['reviews', 'average_rating']);
    }

    public function test_can_show_single_review()
    {
        $event = $this->createEventEnded();
        $review = Review::create([
            'user_id' => $event->user_id,
            'event_id' => $event->id,
            'rating' => 4,
            'comment' => 'Nice event',
        ]);

        $response = $this->getJson(
            str_replace('{eventId}', $event->id,
                str_replace('{reviewId}', $review->id, $this->showRoute))
        );

        $response->assertStatus(200)
            ->assertJsonFragment(['rating'=>4,'comment'=>'Nice event']);
    }

    public function test_unauthenticated_user_cannot_create_review()
    {
        $event = $this->createEventEnded();
        $response = $this->postJson(str_replace('{eventId}', $event->id, $this->storeRoute), [
            'rating'=>5,'comment'=>'Awesome!'
        ]);
        $response->assertStatus(401);
    }

    public function test_user_cannot_create_review_before_event_ends()
    {
        $event = $this->createEventNotEnded();
        $user = $this->createUserAndLogin();
        $this->reserveEvent($user, $event);

        $response = $this->postJson(str_replace('{eventId}', $event->id, $this->storeRoute), [
            'rating' => 5,
            'comment'=> 'Good'
        ], $this->authHeader($user));

        $response->assertStatus(422)
            ->assertJson(['error'=>'You cannot review this event before it ends']);
    }

    public function test_user_cannot_create_review_if_not_attended()
    {
        $event = $this->createEventEnded();
        $user = $this->createUserAndLogin();

        $response = $this->postJson(str_replace('{eventId}', $event->id, $this->storeRoute), [
            'rating' => 3,
            'comment'=> 'Was okay'
        ], $this->authHeader($user));

        $response->assertStatus(422)
            ->assertJson(['error'=>'You did not attend this event']);
    }

    public function test_user_can_create_review_after_attending_ended_event()
    {
        $event = $this->createEventEnded();
        $user = $this->createUserAndLogin();
        $this->reserveEvent($user, $event);

        $response = $this->postJson(str_replace('{eventId}', $event->id, $this->storeRoute), [
            'rating' => 5,
            'comment'=> 'Loved it'
        ], $this->authHeader($user));

        $response->assertStatus(201)
            ->assertJsonFragment(['message'=>'Review created successfully']);
    }

    public function test_user_can_update_own_review()
    {
        $event = $this->createEventEnded();
        $user = $this->createUserAndLogin();
        $this->reserveEvent($user, $event);

        $review = Review::create([
            'user_id' => $user->id,
            'event_id'=> $event->id,
            'rating' => 4,
            'comment'=> 'Nice event'
        ]);

        $response = $this->putJson(
            str_replace('{eventId}', $event->id, str_replace('{reviewId}',$review->id,$this->updateRoute)),
            ['rating'=>3,'comment'=>'Actually, it was ok'],
            $this->authHeader($user)
        );

        $response->assertStatus(200)
            ->assertJsonFragment(['message'=>'Review updated successfully','rating'=>3]);
    }

    public function test_user_cannot_update_other_users_review()
    {
        $event = $this->createEventEnded();
        $owner = User::create(['name'=>'Owner','email'=>'owner@example.com','password'=>bcrypt('pass')]);
        $review = Review::create([
            'user_id' => $owner->id,
            'event_id'=> $event->id,
            'rating'=>4,
            'comment'=>'Good'
        ]);

        $user = $this->createUserAndLogin();
        $this->reserveEvent($user, $event);

        $response = $this->putJson(
            str_replace('{eventId}', $event->id, str_replace('{reviewId}',$review->id,$this->updateRoute)),
            ['rating'=>2],
            $this->authHeader($user)
        );

        $response->assertStatus(403)
            ->assertJson(['error'=>'You are not the owner of this review']);
    }

    public function test_user_can_delete_own_review()
    {
        $event = $this->createEventEnded();
        $user = $this->createUserAndLogin();
        $this->reserveEvent($user, $event);

        $review = Review::create([
            'user_id' => $user->id,
            'event_id'=> $event->id,
            'rating'=>5,
            'comment'=>'Excellent'
        ]);

        $response = $this->deleteJson(
            str_replace('{eventId}', $event->id, str_replace('{reviewId}', $review->id, $this->deleteRoute)),
            [],
            $this->authHeader($user)
        );

        $response->assertStatus(200)
            ->assertJson(['message'=>'Review deleted successfully']);
        $this->assertDatabaseMissing('reviews',['id'=>$review->id]);
    }

    public function test_user_cannot_delete_others_review()
    {
        $event = $this->createEventEnded();
        $owner = User::create(['name'=>'Owner','email'=>'owner@example.com','password'=>bcrypt('pass')]);
        $review = Review::create([
            'user_id'=>$owner->id,
            'event_id'=>$event->id,
            'rating'=>4,
            'comment'=>'Good event'
        ]);

        $user = $this->createUserAndLogin();
        $this->reserveEvent($user, $event);

        $response = $this->deleteJson(
            str_replace('{eventId}', $event->id, str_replace('{reviewId}',$review->id,$this->deleteRoute)),
            [],
            $this->authHeader($user)
        );

        $response->assertStatus(403)
            ->assertJson(['error'=>'You are not the owner of this review']);
    }

    // Helper methods for test setup
    private function createEventEnded()
    {
        return Event::create([
            'user_id'=>User::create(['name'=>'E Owner','email'=>'eowner@example.com','password'=>bcrypt('pass')])->id,
            'title'=>'Ended Event',
            'description'=>'Ended Desc',
            'start_date'=> now()->subDays(3),
            'end_date'  => now()->subDay(),
            'booking_deadline'=> now()->subDays(4),
            'attendee_limit'=>100,
            'price'=>10,
            'location'=>'Place'
        ]);
    }

    private function createEventNotEnded()
    {
        return Event::create([
            'user_id'=>User::create(['name'=>'E Owner2','email'=>'eowner2@example.com','password'=>bcrypt('pass')])->id,
            'title'=>'Not Ended Event',
            'description'=>'Future event',
            'start_date'=> now()->addDay(),
            'end_date'=> now()->addDays(2),
            'booking_deadline'=> now(),
            'attendee_limit'=>50,
            'price'=>20,
            'location'=>'Venue'
        ]);
    }

    private function createUserAndLogin()
    {
        $user = User::create([
            'name'=>'Test User',
            'email'=>'testuser@example.com',
            'password'=>bcrypt('pass')
        ]);
        return $user;
    }

    private function reserveEvent($user, $event): void
    {
        Reservation::create([
            'user_id'=>$user->id,
            'event_id'=>$event->id
        ]);
    }

    private function authHeader($user): array
    {
        $token = JWTAuth::fromUser($user);
        return ['Authorization'=>'Bearer '.$token];
    }
}
