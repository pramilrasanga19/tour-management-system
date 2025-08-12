<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Database\Seeder;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $quotations = Quotation::where('is_final', true)->get();
        $methods = ['cash', 'credit_card', 'bank_transfer', 'other'];
        
        foreach ($quotations as $quotation) {
            $enquiry = $quotation->itinerary->enquiry;
            $totalAmount = $quotation->price_per_person * $enquiry->number_of_people;
            
            $paymentCount = rand(1, 3);
            $amountPaid = 0;
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $amount = $i === $paymentCount - 1 
                    ? $totalAmount - $amountPaid  
                    : rand(100, $totalAmount - $amountPaid - 100);
                
                $amountPaid += $amount;
                
                Payment::create([
                    'quotation_id' => $quotation->id,
                    'amount' => $amount,
                    'payment_method' => $methods[array_rand($methods)],
                    'transaction_reference' => 'TRX-' . strtoupper(uniqid())
                ]);
            }
        }
    }
}