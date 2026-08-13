@extends('layouts.app')

@section('title', 'Boardy — лента объявлений')

@section('content')
    <h1>Лента объявлений</h1>

    <form action="/" method="GET" class="search-bar">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Поиск по заголовку или описанию…">
        <button type="submit" class="btn btn-primary">Найти</button>
    </form>

    @php $sidebarCategories = \Illuminate\Support\Facades\DB::table('categories')->orderBy('name')->get(); @endphp

    <div class="card" style="margin-bottom: 22px;">
        <h2>Категории</h2>
        <ul>
            @foreach ($sidebarCategories as $category)
                <li><a href="/?q={{ $category->name }}">{{ $category->name }}</a></li>
            @endforeach
        </ul>
    </div>

    @if ($ads->isEmpty())
        <div class="empty-state">
            <p>Объявлений пока нет.</p>
        </div>
    @else
        @foreach ($ads as $ad)
            <div class="ad-card">
                <div class="ad-card-main">
                    <h3><a href="/ads/{{ $ad->id }}">{{ $ad->title }}</a></h3>
                    <div class="meta">
                        {{ $ad->user->name ?? 'Неизвестный автор' }}
                        · {{ $ad->created_at->format('d.m.Y H:i') }}
                        @if ($ad->category)
                            · <span class="badge">{{ $ad->category->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="price">{{ (new \App\Support\Pricing\PriceFormatterFactory)->make()->format((float) $ad->price) }}</div>
            </div>
        @endforeach

        <div class="pagination-wrap">
            {{ $ads->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection
