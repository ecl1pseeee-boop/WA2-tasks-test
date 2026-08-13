@extends('layouts.app')

@section('title', $ad->title.' — Boardy')

@section('content')
    <div class="card">
        <div class="ad-detail-header">
            <div>
                <h1 style="margin-bottom: 6px;">{{ $ad->title }}</h1>
                <div class="meta">
                    {{ $ad->user->name ?? 'Неизвестный автор' }}
                    · {{ $ad->created_at->format('d.m.Y H:i') }}
                    @if ($ad->category)
                        · <span class="badge {{ \App\Support\CategoryStyle::cssClass($ad->category->slug) }}">{{ $ad->category->name }}</span>
                    @endif
                </div>
            </div>
            <div class="price">{{ (new \App\Support\Pricing\PriceFormatterFactory)->make()->format((float) $ad->price) }}</div>
        </div>

        <p>{{ $ad->description }}</p>
    </div>

    <div class="card" style="margin-top: 22px;">
        <h2>Комментарии</h2>

        @if ($ad->comments->isEmpty())
            <p class="muted">Комментариев пока нет.</p>
        @else
            @foreach ($ad->comments as $comment)
                <div class="comment">
                    <div class="meta">
                        {{ $comment->user->name ?? 'Аноним' }} · {{ $comment->created_at->format('d.m.Y H:i') }}
                    </div>
                    <div>{{ $comment->body }}</div>
                </div>
            @endforeach
        @endif

        @auth
            <form action="/ads/{{ $ad->id }}/comments" method="POST" style="margin-top: 18px;">
                @csrf
                <div class="field">
                    <label for="body">Ваш комментарий</label>
                    <textarea id="body" name="body" required>{{ old('body') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        @else
            <p class="muted" style="margin-top: 14px;">
                <a href="/login">Войдите</a>, чтобы комментировать.
            </p>
        @endauth
    </div>
@endsection
