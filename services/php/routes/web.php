<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// SSR-веб-приложение Boardy: сессии + CSRF (стандартная группа middleware `web`).
// routes/api.php больше не используется — JSON REST отдаёт FastAPI/Python на /api/*
// (nginx маршрутизирует /api/* мимо Laravel, см. routes/api.php).

Route::get('/', [AdController::class, 'index']);

// ВАЖНО: /ads/create должен идти РАНЬШЕ /ads/{id}, иначе роут {id} перехватит "create".
Route::get('/ads/create', [AdController::class, 'create'])->middleware('auth');
Route::post('/ads', [AdController::class, 'store'])->middleware('auth');
Route::get('/ads/{id}', [AdController::class, 'show']);

Route::post('/ads/{ad}/comments', [CommentController::class, 'store'])->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// Вход через GitHub (Socialite/OAuth) — по ТЗ Boardy.
Route::get('/auth/github', [AuthController::class, 'githubRedirect']);
Route::get('/auth/github/callback', [AuthController::class, 'githubCallback']);

Route::get('/users', [UserController::class, 'index']);
