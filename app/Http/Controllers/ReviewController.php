<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use JonPurvis\Squeaky\Rules\Clean;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::with('user')->latest()->get();
        return response()->json($reviews); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->review_status) {
            return response()->json(['message' => 'You have already submitted a review.'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:0|max:5',
            'comment' => ['required','string', new Clean()],
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        return response()->json($review, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => ['sometimes','string', new Clean()],
        ]);

        $review->update($request->all());
        return response()->json($review);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully']);
    }

     public function updateReviewStatus(Request $request)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $user = $request->user();
        $user->review_status = $request->status;
        $user->save();

        return response()->json(['success' => true, 'review_status' => $user->review_status]);
    }
}
