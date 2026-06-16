<style>
.most-used-card {
    grid-area: app;
}

.app-chart {
    height: 330px;
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    gap: 18px;
    padding: 25px 0 10px;
    border-bottom: 1px solid rgba(255,255,255,0.12);
}

.app-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.app-time {
    font-weight: bold;
}

.app-bar {
    width: 48px;
    border-radius: 12px 12px 6px 6px;
    background: linear-gradient(180deg, #34d399, #60a5fa, #a78bfa);
}

.app-name {
    font-size: 12px;
    color: rgba(255,255,255,0.65);
    writing-mode: vertical-rl;
    transform: rotate(180deg);
}
</style>

<section class="card most-used-card">

    <div class="card-title">Most used apps</div>
    <p class="muted">Minutes used today</p>

    <div class="app-chart">
        <div class="app-column">
            <span class="app-time">130m</span>
            <div class="app-bar" style="height:230px;"></div>
            <span class="app-name">VS Code</span>
        </div>

        <div class="app-column">
            <span class="app-time">62m</span>
            <div class="app-bar" style="height:150px;"></div>
            <span class="app-name">Figma</span>
        </div>

        <div class="app-column">
            <span class="app-time">44m</span>
            <div class="app-bar" style="height:115px;"></div>
            <span class="app-name">Terminal</span>
        </div>

        <div class="app-column">
            <span class="app-time">34m</span>
            <div class="app-bar" style="height:90px;"></div>
            <span class="app-name">Notion</span>
        </div>
    </div>

</section>