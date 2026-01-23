<x-guest-layout>
    <style>
        :root {
            --bb-red: #E63946;
            --bb-red-dark: #C42C39;
            --bb-red-light: #F25C68;
        }

        .login-container {
            padding: 2rem 1.5rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--bb-red), var(--bb-red-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            box-shadow: 0 10px 30px rgba(230, 57, 70, 0.3);
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
            color: #2c3e50;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--bb-red);
            background: white;
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
        }

        .form-input::placeholder {
            color: #95a5a6;
        }

        .input-error {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: block;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            accent-color: var(--bb-red);
        }

        .checkbox-label {
            color: #6c757d;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 1.1rem 2rem;
            background: linear-gradient(135deg, var(--bb-red) 0%, var(--bb-red-light) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(230, 57, 70, 0.3);
            margin-top: 1rem;
        }

        .btn-login:active {
            transform: scale(0.97);
        }

        .btn-login:hover {
            box-shadow: 0 12px 30px rgba(230, 57, 70, 0.4);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-password a {
            color: var(--bb-red);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #95a5a6;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            padding: 0 1rem;
        }

        .register-link {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin-top: 2rem;
        }

        .register-link-text {
            color: #6c757d;
            font-size: 0.95rem;
        }

        .register-link a {
            color: var(--bb-red);
            font-weight: 700;
            text-decoration: none;
            margin-left: 0.5rem;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert-status {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);
        }

        @media (min-width: 481px) {
            .login-container {
                max-width: 440px;
                margin: 0 auto;
            }
        }
    </style>

    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="login-logo">
                🛒
            </div>
            <h1 class="login-title">Connexion</h1>
            <p class="login-subtitle">Accédez à votre espace BB Shopping</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert-status">
                {{ session('status') }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <input 
                    id="email" 
                    class="form-input" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="votre@email.com"
                />
                @error('email')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input 
                    id="password" 
                    class="form-input" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                @error('password')
                    <span class="input-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="checkbox-wrapper">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="checkbox-input" 
                    name="remember"
                >
                <label for="remember_me" class="checkbox-label">
                    Se souvenir de moi
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-login">
                Se connecter
            </button>

            <!-- Forgot Password -->
            @if (Route::has('password.request'))
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                </div>
            @endif
        </form>

        <!-- Divider -->
        <div class="divider">
            <span>OU</span>
        </div>

        <!-- Register Link -->
        @if (Route::has('register'))
        <div class="register-link">
            <span class="register-link-text">Vous n'avez pas de compte ?</span>
            <a href="{{ route('register') }}">Créer un compte</a>
        </div>
        @endif
    </div>
</x-guest-layout>