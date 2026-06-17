<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Register</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: white;
            background: linear-gradient(135deg, #0F0C29, #1A1042, #302B63, #24243e);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .register-wrapper {
            width: 100%;
            max-width: 480px;
            padding: 24px;
        }

        .register-card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 22px;
            padding: 30px;
            backdrop-filter: blur(18px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }

        .brand {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
            background: linear-gradient(90deg, #a78bfa, #60a5fa, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: rgba(255,255,255,0.58);
            margin-bottom: 28px;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 16px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            color: rgba(255,255,255,0.68);
            margin-bottom: 7px;
        }

        .input {
            width: 100%;
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.08);
            color: white;
            outline: none;
            font-size: 14px;
        }

        .input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96,165,250,0.16);
        }

        .register-btn {
            width: 100%;
            border: none;
            padding: 13px;
            border-radius: 14px;
            color: white;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            margin-top: 8px;
            background: linear-gradient(135deg, #a78bfa, #60a5fa, #34d399);
        }

        .register-btn:hover {
            filter: brightness(1.08);
        }

        .error-box {
            display: none;
            background: rgba(248,113,113,0.14);
            border: 1px solid rgba(248,113,113,0.28);
            color: #fecaca;
            padding: 12px;
            border-radius: 14px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .bottom-text {
            text-align: center;
            margin-top: 20px;
            color: rgba(255,255,255,0.58);
            font-size: 14px;
        }

        .bottom-text a {
            color: #60a5fa;
            text-decoration: none;
            font-weight: bold;
        }

        .bottom-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="register-wrapper">
    <div class="register-card">

        <div class="brand">FocusTrack</div>

        <p class="subtitle">
            Create your account and start tracking your productivity.
        </p>

        <div id="errorBox" class="error-box"></div>

        <form method="POST" action="/register" id="registerForm">
            @csrf

            <div class="input-group">
                <label>Full Name</label>
                <input
                    class="input"
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    required
                >
            </div>

            <div class="input-group">
                <label>Username</label>
                <input
                    class="input"
                    type="text"
                    name="username"
                    placeholder="Choose a username"
                    required
                >
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input
                    class="input"
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="input-group">
                <label>Password</label>
                <input
                    class="input"
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Create a password"
                    required
                >
            </div>

            <div class="input-group">
                <label>Confirm Password</label>
                <input
                    class="input"
                    type="password"
                    id="confirmPassword"
                    placeholder="Confirm your password"
                    required
                >
            </div>

            <button class="register-btn" type="submit">
                Create Account
            </button>
        </form>

        <div class="bottom-text">
            Already have an account?
            <a href="/login">Login</a>
        </div>

    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {

    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorBox = document.getElementById('errorBox');

    errorBox.style.display = 'none';

    if (password.length < 6) {
        event.preventDefault();

        errorBox.style.display = 'block';
        errorBox.textContent =
            'Password must be at least 6 characters long.';

        return;
    }

    if (password !== confirmPassword) {
        event.preventDefault();

        errorBox.style.display = 'block';
        errorBox.textContent =
            'Passwords do not match.';

        return;
    }
});
</script>

</body>
</html>