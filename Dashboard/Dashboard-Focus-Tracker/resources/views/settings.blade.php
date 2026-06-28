<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Settings</title>
    <link rel="stylesheet" href="/css/settings.css">
    <link rel="stylesheet" href="/css/nav.css">
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