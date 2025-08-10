<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'itinerary_id',
        'title',
        'price_per_person',
        'currency',
        'notes',
        'is_final',
        'unique_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            $quotation->unique_id = Str::uuid();
        });
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function totalPaid()
    {
        return $this->payments()->sum('amount');
    }

    public function remainingAmount()
    {
        $totalPeople = $this->itinerary->enquiry->number_of_people;
        $totalAmount = $this->price_per_person * $totalPeople;
        return $totalAmount - $this->totalPaid();
    }
}