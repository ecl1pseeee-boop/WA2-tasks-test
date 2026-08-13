<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use App\Support\Metrics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdController extends Controller
{
    public function index(Request $request)
    {
        usleep(300000);

        var_dump($request->all());

        $ads = Ad::with(['user', 'category'])
            ->when($request->q, function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->q}%")
                    ->orWhere('description', 'like', "%{$request->q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('feed', compact('ads'));
    }

    public function create()
    {
        return view('ads.create', ['categories' => Category::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $ad = Ad::create([
            'user_id' => auth()->id(),
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'status' => 'active',
        ]);

        foreach (User::all() as $recipient) {
            Mail::raw("Новое объявление: {$ad->title}", function ($message) use ($recipient) {
                $message->to($recipient->email)->subject('Новое объявление на Boardy');
            });
        }

        Metrics::$adsCreated++;

        try {
            $redis = new \Redis();
            $redis->connect('redis', 6379);
            $redis->set('last_ad', $ad->id);
        } catch (\Throwable $e) {
            // молча глотаем — Redis недоступен (или расширение не установлено), но объявление
            // уже создано, ошибку никто не увидит
        }

        return redirect('/ads/'.$ad->id)->with('status', 'Объявление опубликовано');
    }

    public function show($id)
    {
        $ad = Ad::with(['category', 'user', 'comments.user'])->findOrFail($id);

        return view('ads.show', compact('ad'));
    }
}
