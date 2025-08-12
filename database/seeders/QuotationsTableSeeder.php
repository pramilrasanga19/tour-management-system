<?php

namespace Database\Seeders;

use App\Models\Itinerary;
use App\Models\Quotation;
use Illuminate\Database\Seeder;

class QuotationsTableSeeder extends Seeder
{
    public function run()
    {
        $itineraries = Itinerary::all();
        $currencies = ['USD', 'EUR', 'GBP', 'LKR'];
        
        foreach ($itineraries as $itinerary) {
            $isFinal = rand(0, 1);
            
            Quotation::create([
                'itinerary_id' => $itinerary->id,
                'title' => $this->getRandomPackageName(),
                'price_per_person' => rand(500, 5000),
                'currency' => $currencies[array_rand($currencies)],
                'notes' => rand(0, 1) ? $this->getRandomQuotationNotes() : null,
                'is_final' => $isFinal
            ]);
            
            if (!$isFinal && rand(0, 1)) {
                Quotation::create([
                    'itinerary_id' => $itinerary->id,
                    'title' => $this->getRandomPackageName(),
                    'price_per_person' => rand(500, 5000),
                    'currency' => $currencies[array_rand($currencies)],
                    'notes' => 'Draft version - pending approval',
                    'is_final' => false
                ]);
            }
        }
    }
    
    private function getRandomPackageName()
    {
        $types = ['Standard', 'Deluxe', 'Premium', 'VIP', 'Economy'];
        $inclusions = ['Full Board', 'Half Board', 'All Inclusive', 'Breakfast Only'];
        
        return $types[array_rand($types)] . ' Package (' . $inclusions[array_rand($inclusions)] . ')';
    }
    
    private function getRandomQuotationNotes()
    {
        $notes = [
            'Includes all taxes and service charges',
            'Free cancellation up to 30 days before',
            'Child discounts available',
            'Special honeymoon package',
            'Group discounts for 5+ people'
        ];
        return $notes[array_rand($notes)];
    }
}