<?php

namespace App\Http\Controllers;

use App\Support\LegacyStats;

class StatsController extends Controller
{
    public function index()
    {
        $lastCommentAuthor = optional(optional(optional(\App\Models\Comment::latest()->first())->ad)->user)->name;

        return response()->json([
            'ads_count' => LegacyStats::adsCount(),
            'last_comment_author' => $lastCommentAuthor,
        ]);
    }
}
