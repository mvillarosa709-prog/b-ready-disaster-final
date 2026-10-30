<x-guest-layout>
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
</x-guest-layout> 