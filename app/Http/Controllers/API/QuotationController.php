<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Quotation::query();
        
       
        if ($user->isAgent()) {
            $query->whereHas('itinerary.enquiry', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }
        
        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'itinerary_id' => 'required|exists:itineraries,id',
            'title' => 'required|string|max:255',
            'price_per_person' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
            'is_final' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $itinerary = Itinerary::find($request->itinerary_id);
        
       
        if ($request->user()->isAgent() && $itinerary->enquiry->assigned_to !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $quotation = Quotation::create([
            'itinerary_id' => $request->itinerary_id,
            'title' => $request->title,
            'price_per_person' => $request->price_per_person,
            'currency' => $request->currency,
            'notes' => $request->notes,
            'is_final' => $request->is_final,
        ]);

        return response()->json($quotation, 201);
    }

    public function show(Quotation $quotation)
    {
       
        if (request()->user()->isAgent() && $quotation->itinerary->enquiry->assigned_to !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($quotation);
    }

    public function publicShow($uniqueId)
    {
        $quotation = Quotation::where('unique_id', $uniqueId)->firstOrFail();
        return response()->json($quotation);
    }
}