@extends('layouts.app')

@section('title', 'Подать объявление — Boardy')

@section('content')
    <div class="card" style="max-width: 560px; margin: 0 auto;">
        <h1>Подать объявление</h1>

        <form action="/ads" method="POST">
            @csrf

            <div class="field">
                <label for="title">Заголовок</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="description">Описание</label>
                <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="price">Цена, ₽</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" required>
                @error('price')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="category_id">Категория</label>
                <select id="category_id" name="category_id">
                    <option value="">Без категории</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Опубликовать</button>
        </form>
    </div>
@endsection
