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