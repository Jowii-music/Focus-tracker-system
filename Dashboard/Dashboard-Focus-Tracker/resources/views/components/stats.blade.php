@php
    $activeSession = $activeSession ?? null;

    $sessionTitle = $activeSession?->title ?? "Today's Goal";
    $sessionStatus = $activeSession?->status ?? 'not_active';
    $targetSeconds = $activeSession?->target_seconds ?? 0;
@endphp

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
        liveStatusEl.innerHTML = `<span class="status-dot dot-active"></span>Getting Started`;
        return;
    }

    if (status === "focused") {
        liveStatusEl.classList.add("status-focused");
        liveStatusEl.innerHTML = `<span class="status-dot dot-focused"></span>Focused`;
        return;
    }

    if (status === "fully_focused") {
        liveStatusEl.classList.add("status-fully-focused");
        liveStatusEl.innerHTML = `<span class="status-dot dot-fully-focused"></span>Fully Focused`;
        return;
    }

    if (status === "paused") {
        liveStatusEl.classList.add("status-paused");
        liveStatusEl.innerHTML = `<span class="status-dot dot-paused"></span>Break`;
        return;
    }

    if (status === "back_at_it") {
        liveStatusEl.classList.add("status-back");
        liveStatusEl.innerHTML = `<span class="status-dot dot-back"></span>Back at It`;
        return;
    }

    if (status === "completed") {
        liveStatusEl.classList.add("status-completed");

        const resultMap = {
            excellent: "Session Accomplished",
            good: "Session Completed",
            okay: "Session Finished",
            bad: "Session Ended",
            completed: "Session Completed"
        };

        liveStatusEl.innerHTML = `<span class="status-dot dot-completed"></span>${resultMap[result] || "Session Completed"}`;
        return;
    }

    if (status === "cancelled") {
        liveStatusEl.classList.add("status-cancelled");
        liveStatusEl.innerHTML = `<span class="status-dot dot-cancelled"></span>Session Cancelled`;
        return;
    }

    liveStatusEl.classList.add("status-not-active");
    liveStatusEl.innerHTML = `<span class="status-dot dot-not-active"></span>Not Active`;
}

function resetLiveSessionDisplay() {
    liveSessionStatus = "not_active";
    liveTargetSeconds = 0;
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

    if (
        liveSessionStatus !== "active" &&
        liveSessionStatus !== "focused" &&
        liveSessionStatus !== "fully_focused" &&
        liveSessionStatus !== "back_at_it"
    ) {
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

    if (
        liveSessionStatus === "active" ||
        liveSessionStatus === "focused" ||
        liveSessionStatus === "fully_focused" ||
        liveSessionStatus === "back_at_it" ||
        liveSessionStatus === "paused"
    ) {
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