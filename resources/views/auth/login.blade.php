<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-gray-900">{{ __('Welcome back') }}</h1>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Sign in with your Super Admin, Trainer, or Teacher credentials to continue.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-5">
            @if (Route::has('password.request'))
                <a class="underline text-xs text-gray-500 hover:text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @else
                <div></div>
            @endif

            <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:ring-emerald-500 px-6 py-2.5">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Quick Credentials Helper -->
    <div class="mt-7 pt-5 border-t border-slate-200">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2.5 text-center">
            Demo Portal Credentials
        </p>
        <div class="space-y-2">
            <button type="button"
                    onclick="fillCredentials('adminbready@gmail.com', 'adminbready123!')"
                    class="w-full text-left p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-emerald-50 hover:border-emerald-300 transition flex items-center justify-between group">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded bg-indigo-100 text-indigo-700">SUPER ADMIN</span>
                        <span class="text-xs font-semibold text-slate-800">adminbready@gmail.com</span>
                    </div>
                    <span class="text-[11px] text-slate-500">Pass: adminbready123!</span>
                </div>
                <span class="text-xs font-semibold text-emerald-600 group-hover:translate-x-0.5 transition">&rarr;</span>
            </button>

            <button type="button"
                    onclick="fillCredentials('trainerbready@gmail.com', 'trainerbready123!')"
                    class="w-full text-left p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-emerald-50 hover:border-emerald-300 transition flex items-center justify-between group">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800">TRAINER</span>
                        <span class="text-xs font-semibold text-slate-800">trainerbready@gmail.com</span>
                    </div>
                    <span class="text-[11px] text-slate-500">Pass: trainerbready123!</span>
                </div>
                <span class="text-xs font-semibold text-emerald-600 group-hover:translate-x-0.5 transition">&rarr;</span>
            </button>

            <button type="button"
                    onclick="fillCredentials('teacherbready@gmail.com', 'teacherbready123!')"
                    class="w-full text-left p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-emerald-50 hover:border-emerald-300 transition flex items-center justify-between group">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded bg-sky-100 text-sky-800">TEACHER</span>
                        <span class="text-xs font-semibold text-slate-800">teacherbready@gmail.com</span>
                    </div>
                    <span class="text-[11px] text-slate-500">Pass: teacherbready123!</span>
                </div>
                <span class="text-xs font-semibold text-emerald-600 group-hover:translate-x-0.5 transition">&rarr;</span>
            </button>
        </div>
    </div>

    <script>
        function fillCredentials(email, pass) {
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            if (emailInput && passInput) {
                emailInput.value = email;
                passInput.value = pass;
                emailInput.focus();
            }
        }
    </script>
</x-guest-layout>
