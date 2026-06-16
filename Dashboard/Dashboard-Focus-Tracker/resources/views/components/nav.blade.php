<style>
.top-nav {
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 24px;
    background: rgba(15, 12, 41, 0.78);
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(18px);
}

.brand {
    font-size: 20px;
    font-weight: bold;
    background: linear-gradient(90deg, #a78bfa, #60a5fa, #34d399);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.nav-links {
    display: flex;
    gap: 8px;
}

.nav-btn,
.profile-btn {
    border: 1px solid rgba(255,255,255,0.16);
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.75);
    padding: 8px 12px;
    border-radius: 999px;
}

.nav-btn.active {
    background: linear-gradient(135deg, #a78bfa, #60a5fa);
    color: white;
    border: none;
}
</style>

<nav class="top-nav">

    <div class="brand">
        FocusTrack
    </div>

    <div class="nav-links">
        <button class="nav-btn active">Dashboard</button>
        <button class="nav-btn">Notifications</button>
        <button class="nav-btn">Settings</button>
    </div>

    <form method="POST" action="/logout">
        @csrf
        <button class="profile-btn" type="submit">
            {{ session('username') }} ▾
        </button>
    </form>

</nav>