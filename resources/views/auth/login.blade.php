<x-guest-layout>
    <style>
        
        body {
            background-image: url("{{ asset('images/n.gif') }}");  
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        
        .form-container {
            background-color: rgba(255, 255, 255, 0.2); 
            backdrop-filter: blur(5px); 
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); 
            padding: 2rem;
            max-width: 400px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .input-label {
            color: #000000; 
        }

        .text-green-600 {
            color: #ff0000; 
        }

        .border-green-400 {
            border-color: #9AE6B4; 
        }

        .focus\:border-green-600:focus {
            border-color: #48BB78; 
        }

        .focus\:ring-green-300:focus {
            ring-color: #B2F5E1; 
        }

        .btn-green {
            background-color: #68D391; 
            color: white;
        }

        .btn-green:hover {
            background-color: #48BB78; 
        }

        .error-message {
            color: #E53E3E;
        }

        
        .text-white {
            color: #ffffff;
        }
    </style>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Login Form in a Floating Glass-Like Container -->
    <form method="POST" action="{{ route('login') }}" class="form-container">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="input-label" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full border border-green-400 focus:border-green-600 focus:ring focus:ring-green-300 shadow-sm"
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 error-message" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="input-label" />

            <x-text-input 
                id="password" 
                class="block mt-1 w-full border border-green-400 focus:border-green-600 focus:ring focus:ring-green-300" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password" 
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2 error-message" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="rounded border-green-400 text-black shadow-sm focus:ring-green-500" 
                    name="remember" 
                />
                <span class="ms-2 text-sm text-white ml-2">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a 
                    class="underline text-sm text-white hover:text-green-600" 
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 btn-black ml-2">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
