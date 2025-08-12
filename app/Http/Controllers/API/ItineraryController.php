<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItineraryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Itinerary::query();
        
       
        if ($user->isAgent()) {
            $query->whereHas('enquiry', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }
        
        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enquiry_id' => 'required|exists:enquiries,id',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'days' => 'required|array|min:1',
            'days.*.day' => 'required|integer|min:1',
            'days.*.location' => 'required|string|max:255',
            'days.*.activities' => 'required|array',
            'days.*.activities.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $enquiry = Enquiry::find($request->enquiry_id);
        
        
        if ($request->user()->isAgent() && $enquiry->assigned_to !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

     
        $days = collect($request->days);
        $dayNumbers = $days->pluck('day')->sort()->values();
        
        for ($i = 0; $i < $dayNumbers->count(); $i++) {
            if ($dayNumbers[$i] !== $i + 1) {
                return response()->json([
                    'message' => 'Days must be sequential starting from 1'
                ], 422);
            }
        }

        $itinerary = Itinerary::create([
            'enquiry_id' => $request->enquiry_id,
            'title' => $request->title,
            'notes' => $request->notes,
            'days' => $request->days,
        ]);

        return response()->json($itinerary, 201);
    }

    public function show(Itinerary $itinerary)
    {
       
        if (request()->user()->isAgent() && $itinerary->enquiry->assigned_to !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($itinerary);
    }

    public function update(Request $request, Itinerary $itinerary)
    {
       
        if ($request->user()->isAgent() && $itinerary->enquiry->assigned_to !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'notes' => 'nullable|string',
            'days' => 'sometimes|array|min:1',
            'days.*.day' => 'required|integer|min:1',
            'days.*.location' => 'required|string|max:255',
            'days.*.activities' => 'required|array',
            'days.*.activities.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('days')) {
          
            $days = collect($request->days);
            $dayNumbers = $days->pluck('day')->sort()->values();
            
            for ($i = 0; $i < $dayNumbers->count(); $i++) {
                if ($dayNumbers[$i] !== $i + 1) {
                    return response()->json([
                        'message' => 'Days must be sequential starting from 1'
                    ], 422);
                }
            }
        }

        $itinerary->update($request->all());

        return response()->json($itinerary);
    }

        public function destroy(Itinerary $itinerary)
        {
            if (request()->user()->isAgent() && $itinerary->enquiry->assigned_to !== request()->user()->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $itinerary->delete();

            return response()->json([
                'message' => 'Itinerary deleted successfully'
            ], 200);
        }
}