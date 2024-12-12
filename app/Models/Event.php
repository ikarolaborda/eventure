<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'price',
        'attendee_limit',
        'booking_deadline',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'booking_deadline' => 'datetime',
        ];
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reservations');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function remainingTickets(): int
    {
        return count($this->attendee_limit);
    }

    public function isBookingOpen(): bool
    {
        return now() < $this->booking_deadline && $this->remainingTickets() > 0;
    }
}
