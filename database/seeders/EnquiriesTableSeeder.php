<?php

namespace Database\Seeders;

use App\Models\Enquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EnquiriesTableSeeder extends Seeder
{
    public function run()
    {
        $agents = User::where('role', 'agent')->pluck('id'); // Removed 'user' from where clause
        $statuses = ['pending', 'in-progress', 'converted', 'rejected'];
        
        Enquiry::factory()->count(20)->create([
            'status' => function() use ($statuses) {
                return $statuses[array_rand($statuses)];
            },
            'assigned_to' => function() use ($agents) {
                return rand(0, 1) ? $agents->random() : null;
            },
            'travel_start_date' => Carbon::today()->addDays(rand(30, 365)),
            'travel_end_date' => function(array $attributes) {
                return Carbon::parse($attributes['travel_start_date'])->addDays(rand(3, 14));
            },
            'preferred_destinations' => function() {
                $destinations = ['Colombo', 'Kandy', 'Galle', 'Sigiriya', 'Ella', 'Nuwara Eliya', 'Mirissa', 'Trincomalee'];
                return array_slice($destinations, 0, rand(2, 5));
            }
        ]);
    }
}