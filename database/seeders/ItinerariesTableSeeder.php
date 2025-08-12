<?php

namespace Database\Seeders;

use App\Models\Enquiry;
use App\Models\Itinerary;
use Illuminate\Database\Seeder;

class ItinerariesTableSeeder extends Seeder
{
    public function run()
    {
        $enquiries = Enquiry::whereNotNull('assigned_to')
            ->whereNotIn('status', ['rejected'])
            ->pluck('id');
            
        foreach ($enquiries as $enquiryId) {
            $daysCount = rand(3, 14);
            $days = [];
            
            for ($i = 1; $i <= $daysCount; $i++) {
                $days[] = [
                    'day' => $i,
                    'location' => $this->getRandomLocation(),
                    'activities' => $this->getRandomActivities()
                ];
            }
            
            Itinerary::create([
                'enquiry_id' => $enquiryId,
                'title' => $this->getRandomTitle($daysCount),
                'notes' => rand(0, 1) ? $this->getRandomNotes() : null,
                'days' => $days
            ]);
        }
    }
    
    private function getRandomLocation()
    {
        $locations = ['Colombo', 'Kandy', 'Galle', 'Sigiriya', 'Ella', 'Nuwara Eliya', 'Mirissa', 'Trincomalee'];
        return $locations[array_rand($locations)];
    }
    
    private function getRandomActivities()
    {
        $activities = [
            'Arrival', 'Check-in', 'City tour', 'Temple visit', 'Beach time', 
            'Hiking', 'Shopping', 'Cultural show', 'Safari', 'Water sports'
        ];
        return array_slice($activities, 0, rand(1, 4));
    }
    
    private function getRandomTitle($days)
    {
        $types = ['Cultural', 'Adventure', 'Relaxing', 'Luxury', 'Budget'];
        return "$days-Day " . $types[array_rand($types)] . " Tour";
    }
    
    private function getRandomNotes()
    {
        $notes = [
            'Bring comfortable shoes',
            'Includes all entrance fees',
            'Vegetarian meals available',
            'Hotel upgrades possible',
            'Private guide included'
        ];
        return $notes[array_rand($notes)];
    }
}