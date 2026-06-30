<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Login</title>
    <link rel="stylesheet" href="/css/login.css">

</head>

<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="brand">FocusTrack</div>
        <p class="subtitle">Log in to continue tracking your focus.</p>

        @if(session('error'))
            <div class="error-box">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="input-group">
                <label>Email Address</label>
                <input class="input" type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input class="input" type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button class="login-btn" type="submit">Login</button>

            <div class="forgot-wrap">
                <a href="#" class="forgot-link" id="forgotPasswordBtn">
                    Forgot Password?
                </a>
            </div>
        </form>

        <div class="bottom-text">
            Don't have an account?
            <a href="/register">Create one</a>
        </div>

    </div>
</div>

<div class="modal-overlay" id="forgotPasswordModal">
    <div class="modal">
        <h2 class="modal-title">Reset Password</h2>
        <p class="modal-subtitle">
            Enter your email and choose a new password.
        </p>

        <form method="POST" action="/password-reset">
            @csrf

            <div class="input-group">
                <label>Email Address</label>
                <input class="input" type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group">
                <label>New Password</label>
                <input class="input" type="password" name="password" placeholder="Enter new password" required>
            </div>

            <div class="input-group">
                <label>Confirm New Password</label>
                <input class="input" type="password" name="password_confirmation" placeholder="Confirm new password" required>
            </div>

            <div class="modal-actions">
                <button type="submit" class="modal-btn">
                    Reset Password
                </button>

                <button type="button" class="modal-btn cancel-btn" id="closeModal">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
const forgotPasswordModal = document.getElementById('forgotPasswordModal');
const closeModal = document.getElementById('closeModal');

forgotPasswordBtn.addEventListener('click', function(event) {
    event.preventDefault();
    forgotPasswordModal.classList.add('show');
});

closeModal.addEventListener('click', function() {
    forgotPasswordModal.classList.remove('show');
});

forgotPasswordModal.addEventListener('click', function(event) {
    if (event.target === forgotPasswordModal) {
        forgotPasswordModal.classList.remove('show');
    }
});
</script>

</body>
</html>