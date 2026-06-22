@php
    $activeSession = $activeSession ?? null;

    $sessionTitle = $activeSession?->title ?? "Today's Goal";
    $sessionStatus = $activeSession?->status ?? 'not_active';
    $targetSeconds = $activeSession?->target_seconds ?? 0;
    $startedAt = $activeSession?->started_at ?? null;
@endphp

<style>
.live-session-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.reset-countdown {
    font-size: 13px;
    color: rgba(255,255,255,0.55);
    display: none;
}

.reset-countdown.show {
    display: block;
}

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
}

.dot-not-active {
    background: rgba(255,255,255,0.45);
    box-shadow: 0 0 14px rgba(255,255,255,0.25);
}

.dot-active {
    background: #34d399;
    box-shadow: 0 0 14px rgba(52,211,153,0.7);
}

.dot-paused {
    background: #60a5fa;
    box-shadow: 0 0 14px rgba(96,165,250,0.7);
}

.dot-completed {
    background: #a78bfa;
    box-shadow: 0 0 14px rgba(167,139,250,0.7);
}

.dot-cancelled {
    background: #f87171;
    box-shadow: 0 0 14px rgba(248,113,113,0.7);
}

.status-not-active { color: rgba(255,255,255,0.65); }
.status-active { color: #34d399; }
.status-paused { color: #60a5fa; }
.status-completed { color: #a78bfa; }
.status-cancelled { color: #f87171; }

.timer-blue { color: #60a5fa; }
.goal-purple { color: #a78bfa; }
</style>

<section class="card area-stats">
    <div class="live-session-header">
        <div class="card-title">Live session</div>
        <div class="reset-countdown" id="resetCountdown"></div>
    </div>

    <div class="live-session-grid">

        <div class="live-box">
            <div class="live-value status-not-active" id="liveStatus">
                <span class="status-dot dot-not-active"></span>Not Active
            </div>
            <div class="live-label">Current status</div>
        </div>

        <div class="live-box">
            <div class="live-value timer-blue" id="sessionTimer">
                00:00
            </div>
            <div class="live-label">Time remaining</div>
        </div>

        <div class="live-box">
            <div class="live-value goal-purple" id="sessionGoalTitle">
                {{ $sessionTitle }}
            </div>
            <div class="live-label">Session goal</div>
        </div>

    </div>
</section>

<script>
let liveSessionStatus = "{{ $sessionStatus }}";
let liveTargetSeconds = Number("{{ $targetSeconds }}");
let liveStartedAt = "{{ $startedAt }}";
let liveRemainingSeconds = 0;
let resetSeconds = 20;
let resetInterval = null;

const liveStatusEl = document.getElementById("liveStatus");
const sessionTimerEl = document.getElementById("sessionTimer");
const sessionGoalTitleEl = document.getElementById("sessionGoalTitle");
const resetCountdownEl = document.getElementById("resetCountdown");

function formatLiveTime(seconds) {
    seconds = Math.max(0, Number(seconds) || 0);

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hours > 0) {
        return `${hours}:${String(minutes).padStart(2, "0")}:${String(secs).padStart(2, "0")}`;
    }

    return `${minutes}:${String(secs).padStart(2, "0")}`;
}

function setLiveStatus(status, result = null) {
    liveStatusEl.className = "live-value";

    if (status === "active") {
        liveStatusEl.classList.add("status-active");
        liveStatusEl.innerHTML = `<span class="status-dot dot-active"></span>Focused`;
        return;
    }

    if (status === "paused") {
        liveStatusEl.classList.add("status-paused");
        liveStatusEl.innerHTML = `<span class="status-dot dot-paused"></span>Break`;
        return;
    }

    if (status === "completed") {
        liveStatusEl.classList.add("status-completed");

        const resultText = result
            ? result.charAt(0).toUpperCase() + result.slice(1)
            : "Completed";

        liveStatusEl.innerHTML = `<span class="status-dot dot-completed"></span>${resultText}`;
        return;
    }

    if (status === "cancelled") {
        liveStatusEl.classList.add("status-cancelled");
        liveStatusEl.innerHTML = `<span class="status-dot dot-cancelled"></span>Cancelled`;
        return;
    }

    liveStatusEl.classList.add("status-not-active");
    liveStatusEl.innerHTML = `<span class="status-dot dot-not-active"></span>Not Active`;
}

function resetLiveSessionDisplay() {
    liveSessionStatus = "not_active";
    liveTargetSeconds = 0;
    liveStartedAt = "";
    liveRemainingSeconds = 0;

    setLiveStatus("not_active");
    sessionTimerEl.textContent = "00:00";
    sessionGoalTitleEl.textContent = "Today's Goal";

    resetCountdownEl.classList.remove("show");
    resetCountdownEl.textContent = "";
}

function startResetCountdown() {
    resetSeconds = 20;

    resetCountdownEl.classList.add("show");
    resetCountdownEl.textContent = `Resetting in ${resetSeconds}s`;

    clearInterval(resetInterval);

    resetInterval = setInterval(function () {
        resetSeconds--;
        resetCountdownEl.textContent = `Resetting in ${resetSeconds}s`;

        if (resetSeconds <= 0) {
            clearInterval(resetInterval);
            resetLiveSessionDisplay();
        }
    }, 1000);
}

function updateLiveTimer() {
    if (liveSessionStatus === "paused") {
        sessionTimerEl.textContent = formatLiveTime(liveRemainingSeconds);
        return;
    }

    if (liveSessionStatus !== "active") {
        return;
    }

    liveRemainingSeconds--;
    liveRemainingSeconds = Math.max(0, liveRemainingSeconds);

    sessionTimerEl.textContent = formatLiveTime(liveRemainingSeconds);

    if (liveRemainingSeconds <= 0) {
        liveSessionStatus = "completed";
        setLiveStatus("completed", "completed");
        sessionTimerEl.textContent = "00:00";
        startResetCountdown();

        window.dispatchEvent(new CustomEvent("focusSessionTimerFinished"));
    }
}

function updateLiveSession(data) {
    clearInterval(resetInterval);
    resetCountdownEl.classList.remove("show");
    resetCountdownEl.textContent = "";

    liveSessionStatus = data.status ?? "not_active";
    liveTargetSeconds = Number(data.target_seconds ?? 0);

    if (data.title) {
        sessionGoalTitleEl.textContent = data.title;
    }

    setLiveStatus(liveSessionStatus, data.result ?? null);

    if (liveSessionStatus === "active" || liveSessionStatus === "paused") {
        liveRemainingSeconds = Number(data.remaining_seconds ?? liveRemainingSeconds);
        sessionTimerEl.textContent = formatLiveTime(liveRemainingSeconds);
        return;
    }

    if (liveSessionStatus === "completed" || liveSessionStatus === "cancelled") {
        sessionTimerEl.textContent = formatLiveTime(data.remaining_seconds ?? 0);

        if (data.title) {
            sessionGoalTitleEl.textContent = data.title;
        }

        startResetCountdown();
        return;
    }

    resetLiveSessionDisplay();
}

window.addEventListener("focusSessionChanged", function(event) {
    updateLiveSession(event.detail);
});

setLiveStatus(liveSessionStatus);

if (liveSessionStatus === "active" || liveSessionStatus === "paused") {
    liveRemainingSeconds = liveTargetSeconds;
    sessionTimerEl.textContent = formatLiveTime(liveRemainingSeconds);
} else {
    resetLiveSessionDisplay();
}

setInterval(updateLiveTimer, 1000);
</script>