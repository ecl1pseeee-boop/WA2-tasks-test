<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Ad $ad)
    {
        $data = $request->validate([
            'body' => 'required|string',
        ]);

        Comment::create([
            'ad_id' => $ad->id,
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        return redirect('/ads/'.$ad->id);
    }
}
