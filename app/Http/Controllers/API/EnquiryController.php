<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Enquiry::query();
        
        
        if ($user->isAgent()) {
            $query->where('assigned_to', $user->id);
        }
        
       
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        
        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'travel_start_date' => 'required|date|after:today',
            'travel_end_date' => 'required|date|after:travel_start_date',
            'number_of_people' => 'required|integer|min:1',
            'preferred_destinations' => 'required|array',
            'preferred_destinations.*' => 'string|max:255',
            'budget' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $enquiry = Enquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'travel_start_date' => $request->travel_start_date,
            'travel_end_date' => $request->travel_end_date,
            'number_of_people' => $request->number_of_people,
            'preferred_destinations' => $request->preferred_destinations,
            'budget' => $request->budget,
            'status' => 'pending',
        ]);

        return response()->json($enquiry, 201);
    }

    public function assign(Enquiry $enquiry, Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id'
        ]);

        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $enquiry->assignTo($request->agent_id);

        return response()->json($enquiry);
    }

    public function updateStatus(Enquiry $enquiry, Request $request)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'in-progress', 'converted', 'rejected'])]
        ]);

        $user = $request->user();
        
        if ($user->isAgent() && $enquiry->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $enquiry->updateStatus($request->status);

        return response()->json($enquiry);
    }
}