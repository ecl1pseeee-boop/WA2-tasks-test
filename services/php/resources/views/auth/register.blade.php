@extends('layouts.app')

@section('title', 'Регистрация — Boardy')

@section('content')
    <div class="card form-narrow">
        <h1>Регистрация</h1>

        <form action="/register" method="POST">
            @csrf

            <div class="field">
                <label for="name">Имя</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Зарегистрироваться</button>
        </form>

        <p class="auth-links">Уже есть аккаунт? <a href="/login">Войти</a></p>
    </div>
@endsection
