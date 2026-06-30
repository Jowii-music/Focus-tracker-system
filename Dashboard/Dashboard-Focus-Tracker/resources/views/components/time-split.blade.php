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
 
        <div class="slider-wrap">
            <div class="slider-arrow left"></div>
            <input class="time-slider" id="timeWindowSlider" type="range" min="0" max="15" value="8">
            <div class="slider-arrow right"></div>
        </div>
 
        <div class="slider-label" id="timeWindowLabel">08:00 → 16:00</div>
 
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
                    <div class="week-value green">3h 12m</div>
                </div>
 
                <div class="week-item">
                    <div class="week-label">● Break</div>
                    <div class="week-value blue">28m</div>
                </div>
            </div>
 
            <div>
                <div class="week-item">
                    <div class="week-label">● Distracted</div>
                    <div class="week-value yellow">36m</div>
                </div>
 
                <div class="week-item">
                    <div class="week-label">● Too distracted</div>
                    <div class="week-value red">1h 18m</div>
                </div>
            </div>
        </div>
    </div>
</section>
 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
<script>
Chart.defaults.devicePixelRatio = 2.5;
Chart.defaults.font.family = 'Arial, sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.color = 'rgba(255,255,255,0.78)';
 
const dayCanvas = document.getElementById('focusPerformanceChart');
const dayCtx = dayCanvas.getContext('2d');
 
const weekCanvas = document.getElementById('weekFocusChart');
const weekCtx = weekCanvas.getContext('2d');
 
const dayView = document.getElementById('dayView');
const weekView = document.getElementById('weekView');
const timeWindowSlider = document.getElementById('timeWindowSlider');
const timeWindowLabel = document.getElementById('timeWindowLabel');
 
let dayChart;
let weekChart;
 
const fullDayLabels = [
    '00:00', '01:00', '02:00', '03:00', '04:00', '05:00',
    '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
    '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
    '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'
];
 
const fullFocusedData = [
    null, null, null, null, null, null,
    42, 48, 72, 78, 83, 86,
    null, 81, 76, 62, null, null,
    null, null, null, null, null, null
];
 
const fullBreakData = [
    null, null, null, null, null, null,
    null, null, null, null, null, 86,
    86, 86, null, null, null, null,
    null, null, null, null, null, null
];
 
const fullDistractedData = [
    null, null, null, null, null, null,
    null, null, null, null, null, null,
    null, null, null, 49, 38, 29,
    null, null, null, null, null, null
];
 
const fullTooDistractedData = [
    null, null, null, null, null, null,
    null, null, null, null, null, null,
    null, null, null, null, null, 24,
    16, 10, null, null, null, null
];
 
function setActiveTab(button) {
    button.parentElement.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    button.classList.add('active');
}
 
function makeGradient(ctx, color) {
    const gradient = ctx.createLinearGradient(0, 455, 0, 0);
 
    if (color === 'green') {
        gradient.addColorStop(0, 'rgba(52,211,153,0.04)');
        gradient.addColorStop(1, 'rgba(52,211,153,0.30)');
    }
 
    if (color === 'blue') {
        gradient.addColorStop(0, 'rgba(96,165,250,0.08)');
        gradient.addColorStop(1, 'rgba(96,165,250,0.34)');
    }
 
    if (color === 'orange') {
        gradient.addColorStop(0, 'rgba(251,146,60,0.10)');
        gradient.addColorStop(1, 'rgba(251,146,60,0.42)');
    }
 
    if (color === 'red') {
        gradient.addColorStop(0, 'rgba(248,113,113,0.12)');
        gradient.addColorStop(1, 'rgba(248,113,113,0.45)');
    }
 
    return gradient;
}
 
function getWindowData(data, start) {
    return data.slice(start, start + 9);
}
 
function formatHour(hour) {
    return String(hour).padStart(2, '0') + ':00';
}
 
function updateWindowLabel(start) {
    timeWindowLabel.textContent = `${formatHour(start)} → ${formatHour(start + 8)}`;
}
 
function updateDayChart(startHour) {
    dayChart.data.labels = fullDayLabels.slice(startHour, startHour + 9);
 
    dayChart.data.datasets[0].data = getWindowData(fullFocusedData, startHour);
    dayChart.data.datasets[1].data = getWindowData(fullBreakData, startHour);
    dayChart.data.datasets[2].data = getWindowData(fullDistractedData, startHour);
    dayChart.data.datasets[3].data = getWindowData(fullTooDistractedData, startHour);
 
    dayChart.update('none');
    updateWindowLabel(startHour);
}
 
function createDayChart(startHour = 8) {
    if (dayChart) {
        updateDayChart(startHour);
        return;
    }
 
    dayChart = new Chart(dayCanvas, {
        type: 'line',
        data: {
            labels: fullDayLabels.slice(startHour, startHour + 9),
            datasets: [
                {
                    label: 'Focused',
                    data: getWindowData(fullFocusedData, startHour),
                    borderColor: '#34d399',
                    backgroundColor: makeGradient(dayCtx, 'green'),
                    fill: true,
                    tension: 0.38,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#34d399',
                    pointBorderColor: '#34d399',
                    pointBorderWidth: 0,
                    borderWidth: 3
                },
                {
                    label: 'Break',
                    data: getWindowData(fullBreakData, startHour),
                    borderColor: '#60a5fa',
                    backgroundColor: makeGradient(dayCtx, 'blue'),
                    fill: true,
                    tension: 0,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#60a5fa',
                    pointBorderColor: '#60a5fa',
                    pointBorderWidth: 0,
                    borderWidth: 3
                },
                {
                    label: 'Distracted',
                    data: getWindowData(fullDistractedData, startHour),
                    borderColor: '#fb923c',
                    backgroundColor: makeGradient(dayCtx, 'orange'),
                    fill: true,
                    tension: 0.25,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fb923c',
                    pointBorderColor: '#fb923c',
                    pointBorderWidth: 0,
                    borderWidth: 3
                },
                {
                    label: 'Too distracted',
                    data: getWindowData(fullTooDistractedData, startHour),
                    borderColor: '#f87171',
                    backgroundColor: makeGradient(dayCtx, 'red'),
                    fill: true,
                    tension: 0.25,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#f87171',
                    pointBorderColor: '#f87171',
                    pointBorderWidth: 0,
                    borderWidth: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            spanGaps: false,
            animation: {
                duration: 450,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw + '% focus';
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Focus level',
                        color: 'rgba(255,255,255,0.9)',
                        font: {
                            size: 13,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        stepSize: 20,
                        color: 'rgba(255,255,255,0.78)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        callback: value => value + '%'
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.11)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time of day',
                        color: 'rgba(255,255,255,0.9)',
                        font: {
                            size: 13,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        color: 'rgba(255,255,255,0.78)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        maxRotation: 0,
                        autoSkip: false
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.04)'
                    }
                }
            }
        }
    });
 
    updateWindowLabel(startHour);
}
 
function createWeekChart() {
    if (weekChart) {
        weekChart.destroy();
    }
 
    weekChart = new Chart(weekCanvas, {
        type: 'doughnut',
        data: {
            labels: ['Productive', 'Break', 'Distracted', 'Too distracted'],
            datasets: [{
                data: [63, 9, 12, 16],
                backgroundColor: ['#34d399', '#60a5fa', '#fb923c', '#f87171'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '64%',
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 900,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
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
                ctx.fillText('63%', width / 2, height / 2);
                ctx.restore();
            }
        }]
    });
}
 
timeWindowSlider.addEventListener('input', function () {
    createDayChart(Number(this.value));
});
 
function showDayChart(button = null) {
    if (button) setActiveTab(button);
 
    weekView.style.display = 'none';
    dayView.style.display = 'block';
 
    createDayChart(Number(timeWindowSlider.value));
}
 
function showWeekChart(button = null) {
    if (button) setActiveTab(button);
 
    dayView.style.display = 'none';
    weekView.style.display = 'flex';
 
    createWeekChart();
}
 
showDayChart();
</script>