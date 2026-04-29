<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = mb_strtolower(trim($validated['email']));
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Fallback for legacy users saved with plain-text password.
        $legacyUser = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($legacyUser && hash_equals((string) $legacyUser->password, $password)) {
            $legacyUser->forceFill([
                'password' => Hash::make($password),
            ])->save();

            Auth::login($legacyUser, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => mb_strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Bem-vindo ao Futzin!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
