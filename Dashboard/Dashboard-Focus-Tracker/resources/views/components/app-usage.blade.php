@php
    $appUsageRaw = $latestReport->app_useage_json ?? '{}';
    $appUsageData = json_decode($appUsageRaw, true) ?? [];

    arsort($appUsageData);

    $topApps = array_slice($appUsageData, 0, 5, true);

    $labels = [];
    $minutes = [];

    foreach ($topApps as $name => $seconds) {
        $labels[] = $name ?: 'Unknown';
        $minutes[] = round($seconds / 60);
    }

    $topDistracting = [];

    foreach ($appUsageData as $name => $seconds) {
        $lower = strtolower($name);

        if (
            str_contains($lower, 'youtube') ||
            str_contains($lower, 'reddit') ||
            str_contains($lower, 'instagram') ||
            str_contains($lower, 'discord') ||
            str_contains($lower, 'tiktok') ||
            str_contains($lower, 'netflix')
        ) {
            $topDistracting[] = [
                'name' => $name,
                'minutes' => round($seconds / 60)
            ];
        }
    }

    $topDistracting = array_slice($topDistracting, 0, 3);
@endphp

<style>
.app-usage-card {
    height: 100%;
}

.chart-box {
    height: 740px;
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

        @forelse($topDistracting as $app)
            <div class="distracting-row">
                <span>{{ $app['name'] }}</span>
                <strong>{{ $app['minutes'] }}m</strong>
            </div>
        @empty
            <p class="muted">No distracting apps tracked yet.</p>
        @endforelse
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const appUsageLabels = {!! json_encode($labels) !!};
const appUsageMinutes = {!! json_encode($minutes) !!};

const ctx = document.getElementById('appUsageChart');

const gradient = ctx.getContext('2d').createLinearGradient(0, 400, 0, 0);
gradient.addColorStop(0, '#a78bfa');
gradient.addColorStop(0.5, '#60a5fa');
gradient.addColorStop(1, '#34d399');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: appUsageLabels,
        datasets: [{
            data: appUsageMinutes,
            backgroundColor: gradient,
            borderRadius: 10
        }]
    },
    options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return context.label + ': ' + context.raw + ' minutes';
                }
            }
        }
    },
    scales: {
            y: {
                min: 0,
                max: 60,
                ticks: {
                    stepSize: 10,
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