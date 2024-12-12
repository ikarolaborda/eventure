<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Event;
use App\Models\User;
use App\Repositories\Eloquent\EloquentEventRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EloquentEventRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_event()
    {
        $repo = new EloquentEventRepository(new Event());
        $user = User::factory()->create();

        $event = $repo->create([
            'user_id'          => $user->id,
            'title'            => 'Test Event',
            'description'      => 'Some description',
            'start_date'       => now()->addDays(2)->toDateTimeString(),
            'end_date'         => now()->addDays(3)->toDateTimeString(),
            'booking_deadline' => now()->addDay()->toDateTimeString(),
            'attendee_limit'   => 50,
            'price'            => 20.00,
            'location'         => 'Somewhere',
        ]);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Test Event', $event->title);
    }
}
