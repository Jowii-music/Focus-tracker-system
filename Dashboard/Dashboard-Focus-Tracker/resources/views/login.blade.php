<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Login</title>

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

        .login-wrapper {
            width: 100%;
            max-width: 430px;
            padding: 24px;
        }

        .login-card {
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

        .login-btn,
        .modal-btn {
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

        .login-btn:hover,
        .modal-btn:hover {
            filter: brightness(1.08);
        }

        .error-box {
            background: rgba(248,113,113,0.14);
            border: 1px solid rgba(248,113,113,0.28);
            color: #fecaca;
            padding: 12px;
            border-radius: 14px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .success-box {
            background: rgba(52,211,153,0.14);
            border: 1px solid rgba(52,211,153,0.28);
            color: #bbf7d0;
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

        .bottom-text a,
        .forgot-link {
            color: #60a5fa;
            text-decoration: none;
            font-weight: bold;
        }

        .bottom-text a:hover,
        .forgot-link:hover {
            text-decoration: underline;
        }

        .forgot-wrap {
            text-align: center;
            margin-top: 14px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.58);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            width: 430px;
            background: rgba(35,28,72,0.98);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 22px;
            padding: 26px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.45);
        }

        .modal-title {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .modal-subtitle {
            color: rgba(255,255,255,0.58);
            font-size: 14px;
            margin-bottom: 20px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }

        .modal-btn {
            margin-top: 0;
        }

        .cancel-btn {
            background: rgba(255,255,255,0.12);
        }

        .password-error {
            display: none;
            color: #f87171;
            font-size: 13px;
            margin-top: 8px;
        }

        .password-success {
            display: none;
            color: #34d399;
            font-size: 13px;
            margin-top: 8px;
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="brand">FocusTrack</div>
        <p class="subtitle">Log in to continue tracking your focus.</p>

        @if(session('error'))
            <div class="error-box">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="success-box">
                {{ session('success') }}
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

            <button class="login-btn" type="submit">
                Login
            </button>

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
            Enter your email, old password, then your new password.
        </p>

        <div class="input-group">
            <label>Email Address</label>
            <input class="input" type="email" id="resetEmail" placeholder="Enter your email">
        </div>

        <div class="input-group">
            <label>Old Password</label>
            <input class="input" type="password" id="oldPassword" placeholder="Enter old password">
        </div>

        <div class="input-group">
            <label>New Password</label>
            <input class="input" type="password" id="newPassword" placeholder="Enter new password">
        </div>

        <div class="input-group">
            <label>Confirm New Password</label>
            <input class="input" type="password" id="confirmPassword" placeholder="Confirm new password">
        </div>

        <div class="password-error" id="passwordError">
            Fill email and old password. New password must be at least 6 characters and match confirmation.
        </div>

        <div class="password-success" id="passwordSuccess">
            Password reset request accepted.
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn" id="resetPasswordBtn">
                Reset Password
            </button>

            <button type="button" class="modal-btn cancel-btn" id="closeModal">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
const forgotPasswordModal = document.getElementById('forgotPasswordModal');
const closeModal = document.getElementById('closeModal');
const resetPasswordBtn = document.getElementById('resetPasswordBtn');
const passwordError = document.getElementById('passwordError');
const passwordSuccess = document.getElementById('passwordSuccess');

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

resetPasswordBtn.addEventListener('click', function() {
    const email = document.getElementById('resetEmail').value.trim();
    const oldPassword = document.getElementById('oldPassword').value.trim();
    const newPassword = document.getElementById('newPassword').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();

    passwordError.style.display = 'none';
    passwordSuccess.style.display = 'none';

    if (!email || !oldPassword || !newPassword || !confirmPassword || newPassword.length < 6 || newPassword !== confirmPassword) {
        passwordError.style.display = 'block';
        return;
    }

    passwordSuccess.style.display = 'block';
});
</script>

</body>
</html>