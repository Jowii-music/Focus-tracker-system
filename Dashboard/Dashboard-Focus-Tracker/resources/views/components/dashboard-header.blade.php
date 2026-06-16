<style>
.page {
    padding: 24px;
}

.page-header {
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 28px;
    margin-bottom: 6px;
}

.muted {
    color: rgba(255, 255, 255, 0.55);
}
</style>

<header class="page-header">
    <h1>Dashboard</h1>
    <p class="muted">
        Live productivity overview for {{ session('username') }}.
    </p>
</header>