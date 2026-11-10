<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-gray-900">{{ __('Create an account') }}</h1>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Join the B-READY Disaster Preparedness Platform.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />

            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label
                for="password_confirmation"
                :value="__('Confirm Password')"
            />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />
        </div>

        <!-- Role -->
        <div class="mt-4" x-data="{ selectedRole: '{{ old('role', '') }}' }">
            <x-input-label for="role" :value="__('Register as')" />

            <select
                id="role"
                name="role"
                x-model="selectedRole"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required
            >
                <option value="">Select your role</option>
                <option value="teacher">Teacher</option>
                <option value="trainer">DRR Trainer / Expert</option>
            </select>

            <x-input-error :messages="$errors->get('role')" class="mt-2" />

            <!-- Teacher ID Verification Fields -->
            <div x-show="selectedRole === 'teacher'" x-cloak class="mt-4 p-4 rounded-lg bg-indigo-50/70 border border-indigo-100 transition-all">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-sm font-semibold text-indigo-900">Teacher Professional Verification</span>
                </div>

                <div>
                    <x-input-label for="id_number" :value="__('PRC License / DepEd Employee ID Number')" />
                    <x-text-input
                        id="id_number"
                        class="block mt-1 w-full bg-white"
                        type="text"
                        name="id_number"
                        :value="old('id_number')"
                        placeholder="e.g. 1234567 or DEPED-98765"
                    />
                    <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                </div>

                <div class="mt-3">
                    <x-input-label for="id_document" :value="__('Upload Teacher ID (Photo or PDF)')" />
                    <input
                        id="id_document"
                        name="id_document"
                        type="file"
                        accept="image/*,.pdf"
                        class="block mt-1 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer border border-gray-300 rounded-md bg-white p-1"
                    />
                    <p class="text-xs text-gray-500 mt-1">Proof that you are a verified school teacher (JPG, PNG, or PDF max 5MB).</p>
                    <x-input-error :messages="$errors->get('id_document')" class="mt-2" />
                </div>
            </div>

            <!-- DRR Trainer ID Verification Fields -->
            <div x-show="selectedRole === 'trainer'" x-cloak class="mt-4 p-4 rounded-lg bg-emerald-50/70 border border-emerald-100 transition-all">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-sm font-semibold text-emerald-900">DRR Trainer / Expert Accreditation</span>
                </div>

                <div>
                    <x-input-label for="trainer_id_number" :value="__('DRR Accreditation / Organization ID Number')" />
                    <x-text-input
                        id="trainer_id_number"
                        class="block mt-1 w-full bg-white"
                        type="text"
                        name="trainer_id_number"
                        :value="old('id_number')"
                        placeholder="e.g. NDRRMC-DRR-2026-001"
                    />
                    <x-input-error :messages="$errors->get('trainer_id_number')" class="mt-2" />
                </div>

                <div class="mt-3">
                    <x-input-label for="trainer_id_document" :value="__('Upload DRR Trainer ID or Certificate (Photo or PDF)')" />
                    <input
                        id="trainer_id_document"
                        name="trainer_id_document"
                        type="file"
                        accept="image/*,.pdf"
                        class="block mt-1 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer border border-gray-300 rounded-md bg-white p-1"
                    />
                    <p class="text-xs text-gray-500 mt-1">Proof of accreditation as DRR Trainer/Expert (JPG, PNG, or PDF max 5MB).</p>
                    <x-input-error :messages="$errors->get('trainer_id_document')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Register Button -->
        <div class="flex items-center justify-end mt-4">
            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>

    </form>

    <!-- Google Sign-in Option at the bottom -->
    <div class="mt-6">
        <div class="relative my-5">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white px-3 text-slate-400 font-medium">Or</span>
            </div>
        </div>

        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-2.5 border border-slate-300 rounded-lg bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-xs">
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span>Sign in with Google</span>
        </a>
    </div>
</x-guest-layout>