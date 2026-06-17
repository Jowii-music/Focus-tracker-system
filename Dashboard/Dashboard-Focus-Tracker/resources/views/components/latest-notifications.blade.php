<style>
.notification-row {
    display: grid;
    grid-template-columns: 55px 100px 1fr;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    font-size: 14px;
}

.notification-row:last-child {
    border-bottom: none;
}

.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
}

.badge-red {
    background: rgba(248,113,113,0.18);
    color: #f87171;
}

.badge-blue {
    background: rgba(96,165,250,0.18);
    color: #60a5fa;
}

.badge-orange {
    background: rgba(251,146,60,0.18);
    color: #fb923c;
}

.muted {
    color: rgba(255,255,255,0.55);
}

.notification-card {
    height: 100%;
}
</style>

<section class="card notification-card area-notifications">

    <div class="card-title">Latest notifications</div>

    <p class="muted">
        Only the newest alerts are shown here.
    </p>

    <br>
    <br>
    
<div class="notification-row">
    <span class="muted">11:08</span>
    <span class="badge badge-orange">Distracted</span>
    <span>Instagram open for 4 min.</span>
</div>

<div class="notification-row">
    <span class="muted">10:41</span>
    <span class="badge badge-blue">Break</span>
    <span>Break completed. Focus session resumed.</span>
</div>

<div class="notification-row">
    <span class="muted">10:02</span>
    <span class="badge badge-red">Too long</span>
    <span>YouTube active for 15 min.</span>
</div>

<div class="notification-row">
    <span class="muted">09:25</span>
    <span class="badge badge-orange">Distracted</span>
    <span>Reddit open for 6 min.</span>
</div>

</section>