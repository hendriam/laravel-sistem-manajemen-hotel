<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    /** @use HasFactory<\Database\Factories\FloorFactory> */
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'created_by', 'updated_by'
    ];

    public function createdBy()
    { 
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    { 
        return $this->belongsTo(User::class, 'updated_by');
    }
}
