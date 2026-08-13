<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect('/');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return redirect('/');
        }

        return back()->withErrors(['email' => 'Неверные данные'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Вход через GitHub (OAuth 2.0 через Laravel Socialite).
    public function githubRedirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function githubCallback(Request $request)
    {
        try {
            $ghUser = Socialite::driver('github')->user();
        } catch (\Throwable $e) {
            return redirect('/login')->withErrors([
                'email' => 'GitHub-вход недоступен (не настроены ключи OAuth).',
            ]);
        }

        $email = $ghUser->getEmail() ?: ($ghUser->getNickname() . '@github.local');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $ghUser->getName() ?: $ghUser->getNickname() ?: 'GitHub User',
                'password' => Hash::make(bin2hex(random_bytes(8))),
            ]
        );

        Auth::login($user);

        return redirect('/');
    }
}
