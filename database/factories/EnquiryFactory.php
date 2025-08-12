<?php

namespace Database\Factories;

use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EnquiryFactory extends Factory
{
    protected $model = Enquiry::class;

    public function definition()
    {
        $startDate = Carbon::today()->addDays(rand(30, 365));
        $endDate = $startDate->copy()->addDays(rand(3, 14));
        $destinations = ['Colombo', 'Kandy', 'Galle', 'Sigiriya', 'Ella', 'Nuwara Eliya', 'Mirissa', 'Trincomalee'];

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'travel_start_date' => $startDate,
            'travel_end_date' => $endDate,
            'number_of_people' => rand(1, 10),
            'preferred_destinations' => array_slice($destinations, 0, rand(2, 5)),
            'budget' => rand(500, 5000),
            'status' => $this->faker->randomElement(['pending', 'in-progress', 'converted', 'rejected']),
            'assigned_to' => rand(0, 1) ? User::where('role', 'agent')->inRandomOrder()->first()->id : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}