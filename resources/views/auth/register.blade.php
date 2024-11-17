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

        .input-field {
            width: 100%;
            padding: 10px;
            border: 1px solid #9AE6B4;
            border-radius: 5px;
            margin-top: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .input-field:focus {
            border-color: #48BB78;
            outline: none;
            box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.3);
        }

        .btn-green {
            background-color: #3864f5;
            color: white;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
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

    <form method="POST" action="{{ route('register') }}" class="form-container">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="input-label">Name</label>
            <input id="name" class="input-field" type="text" name="name" value="{{ old('name') }}" required autofocus />
            <div class="error-message">{{ $errors->first('name') }}</div>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="input-label">Email</label>
            <input id="email" class="input-field" type="email" name="email" value="{{ old('email') }}" required />
            <div class="error-message">{{ $errors->first('email') }}</div>
        </div>

        <!-- Role Selection -->
        {{-- <div>
            <label for="role" class="input-label">Role</label>
            <select id="role" name="role" class="input-field" required>
                <option value="user">User</option>
                <option value="rider">Rider</option>
            </select>
            <div class="error-message">{{ $errors->first('role') }}</div>
        </div> --}}

        <!-- Password -->
        <div>
            <label for="password" class="input-label">Password</label>
            <input id="password" class="input-field" type="password" name="password" required />
            <div class="error-message">{{ $errors->first('password') }}</div>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="input-label">Confirm Password</label>
            <input id="password_confirmation" class="input-field" type="password" name="password_confirmation" required />
            <div class="error-message">{{ $errors->first('password_confirmation') }}</div>
        </div>

        <!-- Register Button -->
        <div class="center-align mt-2">
            <a class="link" href="{{ route('login') }}">Already registered?</a>
            <button type="submit" class="btn-green">Register</button>
        </div>
    </form>
</x-guest-layout>
