<x-guest-layout>
    <div class="mb-6">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            {{ auth()->user()?->role === 'trainer' ? 'DRR Trainer' : (auth()->user()?->role === 'admin' ? 'Super Admin' : 'Teacher') }} Account
        </div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('Set your new password') }}</h1>
        <p class="mt-1.5 text-sm text-gray-600">
            {{ __('Your account was provisioned by an administrator. Please set a new permanent password before continuing to your dashboard.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="current_password" :value="__('Temporary Password')" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                required autofocus autocomplete="current-password" placeholder="Enter temporary password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                required autocomplete="new-password" placeholder="Minimum 8 characters" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                required autocomplete="new-password" placeholder="Re-type new password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full inline-flex justify-center items-center py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition">
            {{ __('Save New Password & Continue') }}
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-xs text-gray-500 underline hover:text-gray-800">
            {{ __('Sign out and do this later') }}
        </button>
    </form>
</x-guest-layout>
