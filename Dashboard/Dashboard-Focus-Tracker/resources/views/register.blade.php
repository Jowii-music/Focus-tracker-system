<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Register</title>

    <link rel="stylesheet" href="/css/register.css">
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