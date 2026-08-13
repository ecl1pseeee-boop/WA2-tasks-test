@extends('layouts.app')

@section('title', 'Пользователи — Boardy')

@section('content')
    <h1>Все пользователи</h1>
    <p class="muted" style="margin-top: -12px; margin-bottom: 20px;">
        Страница доступна без входа в систему и без проверки прав.
    </p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>E-mail</th>
                    <th>Роль</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge">{{ $user->role }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
