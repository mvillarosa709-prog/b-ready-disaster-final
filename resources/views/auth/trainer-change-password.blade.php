<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">{{ __('Change your password') }}</h1>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('You must choose a new password before continuing to the trainer dashboard.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('trainer.password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="current_password" :value="__('Temporary Password')" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full"
                required autofocus autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button>{{ __('Update Password') }}</x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="text-sm text-gray-600 underline hover:text-gray-900">
            {{ __('Log out') }}
        </button>
    </form>
</x-guest-layout>
