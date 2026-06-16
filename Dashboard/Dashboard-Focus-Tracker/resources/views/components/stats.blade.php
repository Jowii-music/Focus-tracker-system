<style>
.live-session-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 2fr;
    gap: 14px;
}

.live-box {
    padding: 24px 14px;
    border-radius: 16px;
    background: rgba(255,255,255,0.045);
    border: 1px solid rgba(255,255,255,0.09);
    text-align: center;
}

.live-value {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 8px;
}

.live-label {
    font-size: 14px;
    color: rgba(255,255,255,0.58);
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    background: #34d399;
    box-shadow: 0 0 14px rgba(52,211,153,0.7);
}

.focus-green { color: #34d399; }
.timer-blue { color: #60a5fa; }
.goal-purple { color: #a78bfa; }
.alert-orange {color: #fb923c;}
</style>

<section class="card area-stats">

    <div class="card-title">Live session</div>

    <div class="live-session-grid">

        <div class="live-box">
            <div class="live-value focus-green">
                <span class="status-dot"></span>Focused
            </div>
            <div class="live-label">Current status</div>
        </div>

        <div class="live-box">
            <div class="live-value timer-blue" id="sessionTimer">
                18m 43s
            </div>
            <div class="live-label">Time remaining</div>
        </div>

    <div class="live-box">
        <div class="live-value goal-purple">
            Connect Laravel Dashboard To API
        </div>
        <div class="live-label">Session goal</div>
    </div>

    </div>

</section>

<script>
let remainingSeconds = (18 * 60) + 43;

function updateSessionTimer() {
    const timer = document.getElementById('sessionTimer');

    if (!timer) return;

    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;

    timer.textContent = `${minutes}m ${seconds.toString().padStart(2, '0')}s`;

    if (remainingSeconds > 0) {
        remainingSeconds--;
    }
}

setInterval(updateSessionTimer, 1000);
</script>