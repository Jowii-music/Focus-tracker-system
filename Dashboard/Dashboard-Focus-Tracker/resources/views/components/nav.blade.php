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
    text-decoration: none;
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
    text-decoration: none;
    font-size: 13px;
    cursor: pointer;
}

.nav-btn.active {
    background: linear-gradient(135deg, #a78bfa, #60a5fa);
    color: white;
    border: none;
}

.nav-btn.disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.profile-menu-wrap {
    position: relative;
}

.profile-btn {
    min-width: 92px;
}

.profile-menu {
    position: absolute;
    top: 44px;
    right: 0;
    width: 170px;
    padding: 8px;
    border-radius: 16px;
    background: rgba(35, 28, 72, 0.96);
    border: 1px solid rgba(255,255,255,0.14);
    box-shadow: 0 14px 34px rgba(0,0,0,0.32);
    backdrop-filter: blur(18px);
    display: none;
}

.profile-menu.show {
    display: block;
}

.profile-menu-item {
    width: 100%;
    display: block;
    border: none;
    background: transparent;
    color: rgba(255,255,255,0.82);
    text-align: left;
    padding: 10px 12px;
    border-radius: 12px;
    text-decoration: none;
    font-size: 13px;
    cursor: pointer;
}

.profile-menu-item:hover {
    background: rgba(255,255,255,0.08);
    color: white;
}

.logout-item {
    color: #fca5a5;
}

.logout-item:hover {
    background: rgba(248,113,113,0.12);
    color: #fecaca;
}
</style>

<nav class="top-nav">
    <a href="/dashboard" class="brand">FocusTrack</a>

    <div class="nav-links">
        <a href="/dashboard" class="nav-btn {{ request()->is('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="/notifications" class="nav-btn {{ request()->is('notifications') ? 'active' : '' }}">
            Notifications
        </a>

        <a href="/settings" class="nav-btn {{ request()->is('settings') ? 'active' : '' }}">
            Settings
        </a>
    </div>

    <div class="profile-menu-wrap">
        <button class="profile-btn" type="button" id="profileMenuBtn">
            {{ session('username') ?? optional(auth()->user())->username ?? optional(auth()->user())->name ?? 'User' }} ▾
        </button>

        <div class="profile-menu" id="profileMenu">
            <form method="POST" action="/logout">
                @csrf
                <button class="profile-menu-item logout-item" type="submit">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
const profileMenuBtn = document.getElementById("profileMenuBtn");
const profileMenu = document.getElementById("profileMenu");

profileMenuBtn.addEventListener("click", (event) => {
    event.stopPropagation();
    profileMenu.classList.toggle("show");
});

document.addEventListener("click", (event) => {
    if (!profileMenu.contains(event.target) && !profileMenuBtn.contains(event.target)) {
        profileMenu.classList.remove("show");
    }
});

document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        profileMenu.classList.remove("show");
    }
});
</script>