@php
    $productiveSeconds = (int) ($latestReport->productive_seconds ?? 0);
    $distractedSeconds = (int) ($latestReport->distracted_seconds ?? 0);
    $neutralSeconds = (int) ($latestReport->neutral_seconds ?? 0);

    $sessionStatsRaw = $latestReport->session_stats_json ?? '{}';
    $sessionStats = json_decode($sessionStatsRaw, true) ?? [];

    $breakSeconds = (int) ($sessionStats['break_seconds'] ?? $sessionStats['break'] ?? 0);
    $tooDistractedSeconds = (int) ($sessionStats['too_distracted_seconds'] ?? $sessionStats['too_distracted'] ?? 0);

    $focusedMinutes = round($productiveSeconds / 60);
    $breakMinutes = round($breakSeconds / 60);
    $distractedMinutes = round($distractedSeconds / 60);
    $tooDistractedMinutes = round($tooDistractedSeconds / 60);

    $totalMinutes = max(1, $focusedMinutes + $breakMinutes + $distractedMinutes + $tooDistractedMinutes);

    $focusedPercent = round(($focusedMinutes / $totalMinutes) * 100);
    $breakPercent = round(($breakMinutes / $totalMinutes) * 100);
    $distractedPercent = round(($distractedMinutes / $totalMinutes) * 100);
    $tooDistractedPercent = round(($tooDistractedMinutes / $totalMinutes) * 100);

    function formatTimeSplitMinutes($minutes) {
        $minutes = (int) $minutes;

        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;

            return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
        }

        return "{$minutes}m";
    }
@endphp

<style>
.time-split-card {
    height: 100%;
}

.time-layout {
    display: grid;
    grid-template-columns: 1fr 180px;
    gap: 24px;
    align-items: center;
}

.time-chart-box {
    height: 350px;
}

.time-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 14px;
}

.time-header .card-title,
.time-header .tabs {
    margin: 0;
}

.status-box-legend {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    width: calc(100% - 70px);
    margin-top: 20px;
    margin-left: 50px;
    margin-bottom: 0px;
}

.legend-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-align: center;
    color: rgba(255,255,255,0.75);
    font-size: 14px;
    font-weight: 500;
}

.legend-box {
    width: 56px;
    height: 18px;
    border-radius: 5px;
    background: transparent;
}

.legend-focused {
    border: 2px solid #34d399;
    box-shadow: inset 0 0 10px rgba(52,211,153,0.45);
}

.legend-break {
    border: 2px solid #60a5fa;
    box-shadow: inset 0 0 10px rgba(96,165,250,0.45);
}

.legend-distracted {
    border: 2px solid #fb923c;
    box-shadow: inset 0 0 10px rgba(251,146,60,0.45);
}

.legend-too {
    border: 2px solid #f87171;
    box-shadow: inset 0 0 10px rgba(248,113,113,0.45);
}

.week-view {
    display: none;
    align-items: center;
    justify-content: center;
    gap: 48px;
    min-height: 330px;
}

.week-donut-wrap {
    width: 220px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.week-chart-box {
    width: 220px;
    height: 220px;
}

.week-focus-label {
    margin-top: 10px;
    color: rgba(255,255,255,0.55);
    font-size: 14px;
}

.week-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 34px;
}

.week-item {
    margin-bottom: 18px;
}

.week-label {
    font-size: 14px;
    color: rgba(255,255,255,0.55);
    margin-bottom: 4px;
}

.week-value {
    font-size: 28px;
    font-weight: 700;
}

.green { color: #34d399; }
.blue { color: #60a5fa; }
.yellow { color: #fcd34d; }
.red { color: #f87171; }
</style>

<section class="card time-split-card area-time">
    <div class="time-header">
        <div class="card-title">Time split</div>

        <div class="tabs">
            <button class="tab active" type="button" onclick="showDayChart(this)">Today</button>
            <button class="tab" type="button" onclick="showWeekChart(this)">Week</button>
        </div>
    </div>

    <div class="time-day-layout" id="dayView">
        <div class="time-chart-box">
            <canvas id="focusPerformanceChart"></canvas>
        </div>

        <div class="status-box-legend" id="dayLegend">
            <div class="legend-item">
                <span>Focused</span>
                <span class="legend-box legend-focused"></span>
            </div>

            <div class="legend-item">
                <span>Break</span>
                <span class="legend-box legend-break"></span>
            </div>

            <div class="legend-item">
                <span>Distracted</span>
                <span class="legend-box legend-distracted"></span>
            </div>

            <div class="legend-item">
                <span>Too distracted</span>
                <span class="legend-box legend-too"></span>
            </div>
        </div>
    </div>

    <div class="week-view" id="weekView">
        <div class="week-donut-wrap">
            <div class="week-chart-box">
                <canvas id="weekFocusChart"></canvas>
            </div>
            <div class="week-focus-label">focused week</div>
        </div>

        <div class="week-stats">
            <div>
                <div class="week-item">
                    <div class="week-label">● Productive</div>
                    <div class="week-value green">{{ formatTimeSplitMinutes($focusedMinutes) }}</div>
                </div>

                <div class="week-item">
                    <div class="week-label">● Break</div>
                    <div class="week-value blue">{{ formatTimeSplitMinutes($breakMinutes) }}</div>
                </div>
            </div>

            <div>
                <div class="week-item">
                    <div class="week-label">● Distracted</div>
                    <div class="week-value yellow">{{ formatTimeSplitMinutes($distractedMinutes) }}</div>
                </div>

                <div class="week-item">
                    <div class="week-label">● Too distracted</div>
                    <div class="week-value red">{{ formatTimeSplitMinutes($tooDistractedMinutes) }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const focusedPercent = {!! json_encode($focusedPercent) !!};
const breakPercent = {!! json_encode($breakPercent) !!};
const distractedPercent = {!! json_encode($distractedPercent) !!};
const tooDistractedPercent = {!! json_encode($tooDistractedPercent) !!};

const focusedMinutes = {!! json_encode($focusedMinutes) !!};
const breakMinutes = {!! json_encode($breakMinutes) !!};
const distractedMinutes = {!! json_encode($distractedMinutes) !!};
const tooDistractedMinutes = {!! json_encode($tooDistractedMinutes) !!};

const dayCanvas = document.getElementById('focusPerformanceChart');
const dayCtx = dayCanvas.getContext('2d');

const weekCanvas = document.getElementById('weekFocusChart');
const weekCtx = weekCanvas.getContext('2d');

const dayView = document.getElementById('dayView');
const weekView = document.getElementById('weekView');

let dayChart;
let weekChart;

function setActiveTab(button) {
    button.parentElement.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    button.classList.add('active');
}

function makeGradient(ctx, color) {
    const gradient = ctx.createLinearGradient(0, 360, 0, 0);

    if (color === 'green') {
        gradient.addColorStop(0, 'rgba(52,211,153,0.05)');
        gradient.addColorStop(1, 'rgba(52,211,153,0.28)');
    }

    if (color === 'blue') {
        gradient.addColorStop(0, 'rgba(96,165,250,0.08)');
        gradient.addColorStop(1, 'rgba(96,165,250,0.32)');
    }

    if (color === 'orange') {
        gradient.addColorStop(0, 'rgba(251,146,60,0.12)');
        gradient.addColorStop(1, 'rgba(251,146,60,0.42)');
    }

    if (color === 'red') {
        gradient.addColorStop(0, 'rgba(248,113,113,0.12)');
        gradient.addColorStop(1, 'rgba(248,113,113,0.42)');
    }

    return gradient;
}

function createDayChart() {
    if (dayChart) {
        dayChart.destroy();
    }

    dayChart = new Chart(dayCanvas, {
        type: 'line',
        data: {
            labels: [
                '08:00',
                '09:00',
                '10:00',
                '11:00',
                '12:00',
                '13:00',
                '14:00',
                '15:00',
                '16:00',
                '17:00',
                '18:00'
            ],
            datasets: [
                {
                    label: 'Focused',
                    data: [focusedPercent, focusedPercent, focusedPercent, null, focusedPercent, focusedPercent, focusedPercent, focusedPercent, null, null, null],
                    borderColor: '#34d399',
                    backgroundColor: makeGradient(dayCtx, 'green'),
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    borderWidth: 3
                },
                {
                    label: 'Break',
                    data: [null, null, breakPercent, breakPercent, breakPercent, null, null, null, null, null, null],
                    borderColor: '#60a5fa',
                    backgroundColor: makeGradient(dayCtx, 'blue'),
                    fill: true,
                    tension: 0,
                    pointRadius: 4,
                    borderWidth: 3
                },
                {
                    label: 'Distracted',
                    data: [null, null, null, null, null, null, null, distractedPercent, distractedPercent, distractedPercent, null],
                    borderColor: '#fb923c',
                    backgroundColor: makeGradient(dayCtx, 'orange'),
                    fill: true,
                    tension: 0.25,
                    pointRadius: 4,
                    borderWidth: 3
                },
                {
                    label: 'Too distracted',
                    data: [null, null, null, null, null, null, null, null, null, tooDistractedPercent, tooDistractedPercent],
                    borderColor: '#f87171',
                    backgroundColor: makeGradient(dayCtx, 'red'),
                    fill: true,
                    tension: 0.25,
                    pointRadius: 4,
                    borderWidth: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            spanGaps: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const minuteMap = {
                                'Focused': focusedMinutes,
                                'Break': breakMinutes,
                                'Distracted': distractedMinutes,
                                'Too distracted': tooDistractedMinutes
                            };

                            return context.dataset.label + ': ' + minuteMap[context.dataset.label] + 'm';
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        color: 'rgba(255,255,255,0.65)',
                        callback: value => value + '%'
                    },
                    grid: { color: 'rgba(255,255,255,0.12)' }
                },
                x: {
                    ticks: {
                        color: 'rgba(255,255,255,0.65)',
                        maxRotation: 0,
                        autoSkip: false
                    },
                    grid: { color: 'rgba(255,255,255,0.04)' }
                }
            }
        }
    });
}

function createWeekChart() {
    if (weekChart) {
        weekChart.destroy();
    }

    const centerValue = focusedPercent + '%';

    weekChart = new Chart(weekCanvas, {
        type: 'doughnut',
        data: {
            labels: ['Productive', 'Break', 'Distracted', 'Too distracted'],
            datasets: [{
                data: [focusedPercent, breakPercent, distractedPercent, tooDistractedPercent],
                backgroundColor: ['#34d399', '#60a5fa', '#fb923c', '#f87171'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '64%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const minutes = [
                                focusedMinutes,
                                breakMinutes,
                                distractedMinutes,
                                tooDistractedMinutes
                            ];

                            return context.label + ': ' + minutes[context.dataIndex] + 'm';
                        }
                    }
                }
            }
        },
        plugins: [{
            id: 'centerText',
            beforeDraw(chart) {
                const { width, height, ctx } = chart;

                ctx.save();
                ctx.font = 'bold 34px Arial';
                ctx.fillStyle = 'white';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(centerValue, width / 2, height / 2);
                ctx.restore();
            }
        }]
    });
}

function showDayChart(button = null) {
    if (button) setActiveTab(button);

    weekView.style.display = 'none';
    dayView.style.display = 'grid';

    createDayChart();
}

function showWeekChart(button = null) {
    if (button) setActiveTab(button);

    dayView.style.display = 'none';
    weekView.style.display = 'flex';

    createWeekChart();
}

showDayChart();
</script>