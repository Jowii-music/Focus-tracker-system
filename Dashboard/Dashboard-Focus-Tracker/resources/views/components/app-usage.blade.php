<style>
.app-usage-card {
    height: 100%;
}

.chart-box {
    height: 540px;
    margin-top: 20px;
}

.distracting-box {
    margin-top: 36px;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.15);
}

.distracting-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.10);
}
</style>

<section class="card app-usage-card area-app">
    <div class="card-title">Most used apps</div>
    <p class="muted">Minutes used today</p>

    <div class="chart-box">
        <canvas id="appUsageChart"></canvas>
    </div>

    <div class="distracting-box">
        <div class="card-title">Top distracting apps</div>

        <div class="distracting-row">
            <span>YouTube</span>
            <strong>44m</strong>
        </div>

        <div class="distracting-row">
            <span>Reddit</span>
            <strong>34m</strong>
        </div>

        <div class="distracting-row">
            <span>Instagram</span>
            <strong>4m</strong>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('appUsageChart');

const gradient = ctx.getContext('2d').createLinearGradient(0, 400, 0, 0);
gradient.addColorStop(0, '#a78bfa');
gradient.addColorStop(0.5, '#60a5fa');
gradient.addColorStop(1, '#34d399');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['VS Code', 'Figma', 'Notion', 'Chrome', 'Terminal'],
        datasets: [{
            data: [130, 62, 34, 28, 19],
            backgroundColor: gradient,
            borderRadius: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                min: 0,
                max: 180,
                ticks: {
                    stepSize: 20,
                    color: 'rgba(255,255,255,0.65)',
                    callback: value => value + 'm'
                },
                grid: {
                    color: 'rgba(255,255,255,0.12)'
                }
            },
            x: {
                ticks: {
                    color: 'rgba(255,255,255,0.65)'
                },
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>