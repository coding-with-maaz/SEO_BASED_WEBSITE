<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Nazaarabox</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .login-container {
            background: rgba(30, 30, 30, 0.95);
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo h1 {
            color: #E50914;
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
        }
        .logo p {
            color: #9ca3af;
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            color: #e5e7eb;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(17, 24, 39, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #E50914;
            box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.1);
        }
        .form-input::placeholder {
            color: #6b7280;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .remember-me input {
            margin-right: 0.5rem;
            accent-color: #E50914;
        }
        .remember-me label {
            color: #d1d5db;
            font-size: 0.875rem;
            cursor: pointer;
        }
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: #E50914;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: #b8070f;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .error-message {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Nazaarabox</h1>
            <p>Admin Panel Login</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input @error('email') border-red-500 @enderror" 
                    placeholder="admin@example.com"
                    value="{{ old('email') }}"
                    required 
                    autofocus
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') border-red-500 @enderror" 
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-login">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>

