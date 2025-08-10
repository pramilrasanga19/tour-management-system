<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Payment::query();
        
       
        if ($user->isAgent()) {
            $query->whereHas('quotation.itinerary.enquiry', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }
        
       
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->has('assigned_to')) {
            $query->whereHas('quotation.itinerary.enquiry', function($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            });
        }
        
        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }
        
        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quotation_id' => 'required|exists:quotations,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,other',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $quotation = Quotation::find($request->quotation_id);
        
        if ($request->user()->isAgent() && $quotation->itinerary->enquiry->assigned_to !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment = Payment::create([
            'quotation_id' => $request->quotation_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
        ]);

        return response()->json($payment, 201);
    }
}