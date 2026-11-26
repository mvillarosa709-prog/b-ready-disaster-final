<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $wasForcedChange = (bool) $user->must_change_password;
        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->save();

        if ($wasForcedChange || $request->routeIs('trainer.password.update')) {
            $targetRoute = match ($user->role) {
                'admin' => route('admin.dashboard'),
                'trainer' => route('trainer.dashboard'),
                default => route('teacher.dashboard'),
            };

            return redirect($targetRoute)
                ->with('status', 'password-updated')
                ->with('success', 'Your password has been updated successfully. Welcome to B-READY!');
        }

        return back()->with('status', 'password-updated');
    }
}
