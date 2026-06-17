<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Settings</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: white;
            background: linear-gradient(135deg, #0F0C29, #1A1042, #302B63, #24243e);
            overflow: hidden;
        }

        .page {
            height: calc(100vh - 64px);
            padding: 18px 24px;
        }

        .settings-container {
            max-width: 1500px;
            height: 100%;
            margin: 0 auto;
        }

        .page-title {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .muted {
            color: rgba(255,255,255,0.55);
            font-size: 13px;
        }

        .settings-layout {
            height: calc(100% - 54px);
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 16px;
            margin-top: 14px;
        }

        .left-column {
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 16px;
            min-height: 0;
        }

        .card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 18px;
            padding: 16px;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.22);
            min-height: 0;
        }

        .card-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .input-group {
            margin-bottom: 10px;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            color: rgba(255,255,255,0.65);
            margin-bottom: 5px;
        }

        .input {
            width: 100%;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: white;
            border-radius: 11px;
            padding: 9px 11px;
            outline: none;
        }

        .password-btn,
        .save-btn,
        .modal-btn {
            border: none;
            padding: 11px 18px;
            border-radius: 12px;
            cursor: pointer;
            color: white;
            font-weight: bold;
            background: linear-gradient(135deg, #a78bfa, #60a5fa, #34d399);
        }

        .password-btn {
            width: 100%;
            margin-top: 18px;
        }

        .notification-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .notification-box {
            border-radius: 16px;
            padding: 14px;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            transition: 0.25s;
        }

        .notification-box:hover {
            transform: translateY(-2px);
            background: rgba(255,255,255,0.08);
        }

        .notification-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .notification-title {
            font-weight: bold;
            font-size: 14px;
        }

        input[type="checkbox"] { display: none; }

        .switch {
            width: 44px;
            height: 23px;
            border-radius: 999px;
            position: relative;
            transition: 0.25s;
            background: rgba(156,163,175,0.45);
        }

        .switch::after {
            content: "";
            position: absolute;
            width: 17px;
            height: 17px;
            top: 3px;
            left: 3px;
            border-radius: 50%;
            background: white;
            transition: 0.25s;
        }

        input[type="checkbox"]:checked + .switch::after {
            left: 24px;
        }

        input[type="checkbox"]:checked + .switch.logo {
            background: linear-gradient(135deg, #a78bfa, #60a5fa, #34d399);
        }

        input[type="checkbox"]:checked + .switch.green { background: #34d399; }
        input[type="checkbox"]:checked + .switch.blue { background: #60a5fa; }
        input[type="checkbox"]:checked + .switch.red { background: #f87171; }

        .app-card {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .app-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }

        .classification-legend {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .legend-pill {
            padding: 6px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .legend-productive {
            background: rgba(52,211,153,0.18);
            color: #34d399;
            border: 1px solid rgba(52,211,153,0.35);
        }

        .legend-neutral {
            background: rgba(96,165,250,0.18);
            color: #60a5fa;
            border: 1px solid rgba(96,165,250,0.35);
        }

        .legend-distracting {
            background: rgba(248,113,113,0.18);
            color: #f87171;
            border: 1px solid rgba(248,113,113,0.35);
        }

        .app-list {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px 12px;
        }

        .app-row {
            display: grid;
            grid-template-columns: 1fr 205px;
            gap: 10px;
            align-items: center;
            padding: 9px 10px;
            border-radius: 14px;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.08);
            font-size: 14px;
        }

        .class-toggle {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: rgba(255,255,255,0.08);
            padding: 4px;
            border-radius: 999px;
        }

        .class-toggle input {
            display: none;
        }

        .class-toggle label {
            text-align: center;
            padding: 6px 7px;
            font-size: 11px;
            border-radius: 999px;
            cursor: pointer;
            transition: 0.2s;
            color: rgba(255,255,255,0.65);
        }

        .class-toggle input:checked + label.productive {
            background: #34d399;
            color: #071b13;
            font-weight: bold;
        }

        .class-toggle input:checked + label.neutral {
            background: #60a5fa;
            color: #071527;
            font-weight: bold;
        }

        .class-toggle input:checked + label.distracting {
            background: #f87171;
            color: #2a0707;
            font-weight: bold;
        }

        .save-btn {
            margin-top: 12px;
            width: 170px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            width: 420px;
            background: rgba(35,28,72,0.98);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.45);
        }

        .modal-title {
            font-size: 20px;
            margin-bottom: 16px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }

        .cancel-btn {
            background: rgba(255,255,255,0.10);
        }

        .password-error {
            color: #f87171;
            font-size: 13px;
            margin-top: 8px;
            display: none;
        }
    </style>
</head>

<body>

@include('components.nav')

<main class="page">
    <div class="settings-container">

        <h1 class="page-title">Settings</h1>
        <p class="muted">Manage your profile, notifications and app classification.</p>

        <form method="POST" action="/settings/update" id="settingsForm">
            @csrf

            <div class="settings-layout">

                <div class="left-column">

                    <section class="card">
                        <div class="card-title">Account Settings</div>

                        <div class="profile-grid">
                            <div>
                                <div class="input-group">
                                    <label>Full Name</label>
                                    <input class="input" type="text" name="name" value="{{ $user->name ?? '' }}">
                                </div>

                                <div class="input-group">
                                    <label>Username</label>
                                    <input class="input" type="text" name="username" value="{{ $user->username ?? session('username') }}">
                                </div>

                                <div class="input-group">
                                    <label>Email</label>
                                    <input class="input" type="email" name="email" value="{{ $user->email ?? '' }}">
                                </div>
                            </div>

                            <div>
                                <p class="muted">
                                    Change your password from a secure popup. Old password is required first.
                                </p>

                                <button type="button" class="password-btn" id="openPasswordModal">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </section>

                    <section class="card">
                        <div class="card-title">Notifications</div>

                        <div class="notification-grid">

                            <label class="notification-box">
                                <div class="notification-top">
                                    <span class="notification-title">Session Completed</span>
                                    <input type="checkbox" name="session_completed" checked>
                                    <span class="switch logo"></span>
                                </div>
                                <p class="muted">Notify when a focus session ends.</p>
                            </label>

                            <label class="notification-box">
                                <div class="notification-top">
                                    <span class="notification-title">Focus Streak</span>
                                    <input type="checkbox" name="focus_streak" checked>
                                    <span class="switch green"></span>
                                </div>
                                <p class="muted">Notify when you stay focused.</p>
                            </label>

                            <label class="notification-box">
                                <div class="notification-top">
                                    <span class="notification-title">Break Reminders</span>
                                    <input type="checkbox" name="break_reminders" checked>
                                    <span class="switch blue"></span>
                                </div>
                                <p class="muted">Remind you to take breaks.</p>
                            </label>

                            <label class="notification-box">
                                <div class="notification-top">
                                    <span class="notification-title">Distraction Alerts</span>
                                    <input type="checkbox" name="distraction_alerts" checked>
                                    <span class="switch red"></span>
                                </div>
                                <p class="muted">Warn when distraction takes too long.</p>
                            </label>

                        </div>

                        <button class="save-btn" type="submit">
                            Save Settings
                        </button>
                    </section>

                </div>

                <section class="card app-card">
                    <div class="app-card-header">
                        <div class="card-title" style="margin-bottom: 0;">App Classification</div>

                        <div class="classification-legend">
                            <span class="legend-pill legend-productive">Productive</span>
                            <span class="legend-pill legend-neutral">Neutral</span>
                            <span class="legend-pill legend-distracting">Distracting</span>
                        </div>
                    </div>

                    <div class="app-list">
                        @php
                            $apps = [
                                'VS Code' => 'productive',
                                'PHPStorm' => 'productive',
                                'Figma' => 'productive',
                                'Terminal' => 'productive',
                                'MySQL Workbench' => 'productive',
                                'Notion' => 'productive',
                                'Word' => 'productive',
                                'Excel' => 'productive',
                                'Chrome' => 'neutral',
                                'Edge' => 'neutral',
                                'Teams' => 'neutral',
                                'Discord' => 'neutral',
                                'Spotify' => 'neutral',
                                'YouTube' => 'distracting',
                                'Instagram' => 'distracting',
                                'TikTok' => 'distracting',
                                'Reddit' => 'distracting',
                                'Netflix' => 'distracting',
                                'Steam' => 'distracting',
                                'Twitch' => 'distracting',
                            ];
                        @endphp

                        @foreach($apps as $app => $type)
                            @php $safe = strtolower(str_replace([' ', '.'], '_', $app)); @endphp

                            <div class="app-row">
                                <span>{{ $app }}</span>

                                <div class="class-toggle">
                                    <input type="radio" id="{{ $safe }}_productive" name="apps[{{ $app }}]" value="productive" {{ $type === 'productive' ? 'checked' : '' }}>
                                    <label for="{{ $safe }}_productive" class="productive">Pro</label>

                                    <input type="radio" id="{{ $safe }}_neutral" name="apps[{{ $app }}]" value="neutral" {{ $type === 'neutral' ? 'checked' : '' }}>
                                    <label for="{{ $safe }}_neutral" class="neutral">Neu</label>

                                    <input type="radio" id="{{ $safe }}_distracting" name="apps[{{ $app }}]" value="distracting" {{ $type === 'distracting' ? 'checked' : '' }}>
                                    <label for="{{ $safe }}_distracting" class="distracting">Dis</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

            </div>
        </form>

    </div>
</main>

<div class="modal-overlay" id="passwordModal">
    <div class="modal">
        <h2 class="modal-title">Change Password</h2>

        <div class="input-group">
            <label>Old Password</label>
            <input class="input" type="password" id="oldPassword">
        </div>

        <div class="input-group">
            <label>New Password</label>
            <input class="input" type="password" id="newPassword">
        </div>

        <div class="input-group">
            <label>Confirm New Password</label>
            <input class="input" type="password" id="confirmPassword">
        </div>

        <div class="password-error" id="passwordError">
            Old password is required. New password must match and be at least 6 characters.
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn" id="savePasswordBtn">Save Password</button>
            <button type="button" class="modal-btn cancel-btn" id="closePasswordModal">Cancel</button>
        </div>
    </div>
</div>

<script>
const passwordModal = document.getElementById('passwordModal');
const openPasswordModal = document.getElementById('openPasswordModal');
const closePasswordModal = document.getElementById('closePasswordModal');
const savePasswordBtn = document.getElementById('savePasswordBtn');
const passwordError = document.getElementById('passwordError');

openPasswordModal.addEventListener('click', () => {
    passwordModal.classList.add('show');
});

closePasswordModal.addEventListener('click', () => {
    passwordModal.classList.remove('show');
});

passwordModal.addEventListener('click', (event) => {
    if (event.target === passwordModal) {
        passwordModal.classList.remove('show');
    }
});

savePasswordBtn.addEventListener('click', () => {
    const oldPassword = document.getElementById('oldPassword').value.trim();
    const newPassword = document.getElementById('newPassword').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();

    if (!oldPassword || !newPassword || !confirmPassword || newPassword !== confirmPassword || newPassword.length < 6) {
        passwordError.style.display = 'block';
        return;
    }

    passwordError.style.display = 'none';
    passwordModal.classList.remove('show');
});
</script>

</body>
</html>