<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'travel_start_date',
        'travel_end_date',
        'number_of_people',
        'preferred_destinations',
        'budget',
        'status',
        'assigned_to'
    ];

    protected $casts = [
        'travel_start_date' => 'date',
        'travel_end_date' => 'date',
        'preferred_destinations' => 'array',
    ];

    public function assignTo($agentId)
    {
        $this->update(['assigned_to' => $agentId]);
    }

    public function updateStatus($status)
    {
        $validStatuses = ['pending', 'in-progress', 'converted', 'rejected'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $this->update(['status' => $status]);
        return true;
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }
}