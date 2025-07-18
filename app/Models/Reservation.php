<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'reservation_number',
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function total_paid()
    {
        return $this->payments()->sum('amount');
    }
}
