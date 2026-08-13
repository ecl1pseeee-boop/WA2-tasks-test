@extends('layouts.app')

@section('title', 'Вход — Boardy')

@section('content')
    <div class="card form-narrow">
        <h1>Вход</h1>

        <form action="/login" method="POST">
            @csrf

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="field">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Войти</button>
        </form>

        <div style="margin: 14px 0; text-align: center; color: var(--color-muted);">или</div>

        <a href="/auth/github" class="btn" style="display: block; width: 100%; text-align: center; background: #24292f; color: #fff;">Войти через GitHub</a>

        <p class="auth-links">Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
    </div>
@endsection
