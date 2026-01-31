<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Your existing CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #2e2e2e;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 400px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            margin-bottom: 5px;
            color: #ccc;
        }

        .login-container input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #4caf50;
            border-radius: 5px;
            background-color: #3a3a3a;
            color: #fff;
        }

        .login-container .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .login-container .checkbox-container input {
            margin-right: 10px;
        }

        .login-container .checkbox-container label {
            margin: 0;
        }

        .login-container .buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .login-container .buttons a,
        .login-container .buttons button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }

        .login-container .buttons button.logout-button {
            background-color: #f44336;
        }

        .login-container .buttons a:hover,
        .login-container .buttons button:hover {
            background-color: #3a3a3a;
        }

        .login-container .forgot-password {
            margin-top: 10px;
            text-align: center;
        }

        .login-container .forgot-password a {
            color: #4caf50;
            text-decoration: none;
        }

        .error {
            color: #f44336;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <!-- Display Errors -->
        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" :value="old('email')" required autofocus autocomplete="username">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            <div class="checkbox-container">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Remember me</label>
            </div>
            <div class="buttons">
                <a href="{{ route('register') }}">Register</a>
                <button type="submit" class="btn btn-primary">Log in</button>
            </div>
        </form>
        <div class="forgot-password">
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Forgot your password?</a>
            @endif
        </div>
    </div>
</body>

</html>
