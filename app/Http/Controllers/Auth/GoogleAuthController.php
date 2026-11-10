<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth or display the Google account selector.
     */
    public function redirect(Request $request): View|RedirectResponse
    {
        // If real Socialite and Google credentials are fully configured in .env:
        if (
            class_exists(\Laravel\Socialite\Facades\Socialite::class) &&
            config('services.google.client_id') &&
            config('services.google.client_secret')
        ) {
            return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
        }

        // Development / Interactive Google Sign-In prompt
        $existingUsers = User::query()
            ->select(['id', 'name', 'email', 'role'])
            ->orderBy('id', 'asc')
            ->get();

        return view('auth.google-prompt', [
            'users' => $existingUsers,
        ]);
    }

    /**
     * Handle simulated or returned Google account login.
     */
    public function select(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'in:teacher,trainer'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            // New user registering via Google: needs a role
            $role = $request->role ?? 'teacher';
            $name = $request->name ?: explode('@', $request->email)[0];

            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(24)),
                'role' => $role,
                'verification_status' => 'approved',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(
            $user->role === 'trainer' ? route('trainer.dashboard') : route('teacher.dashboard')
        );
    }
}

