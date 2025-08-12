<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            EnquiriesTableSeeder::class,
            ItinerariesTableSeeder::class,
            QuotationsTableSeeder::class,
            PaymentsTableSeeder::class,
        ]);
    }
}