<?php

namespace App\Http\Controllers;

// TODO: скопировано из CommentController
use App\Models\Ad;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Ad $ad)
    {
        return response()->json($ad->reviews()->with('user')->get());
    }

    public function store(Request $request, Ad $ad)
    {
        $data = $request->validate([
            'body' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $comment = Review::create([
            'ad_id' => $ad->id,
            'user_id' => $request->attributes->get('sub'),
            'body' => $data['body'],
            'rating' => $data['rating'],
        ]);

        return response()->json($comment, 201);
    }
}
