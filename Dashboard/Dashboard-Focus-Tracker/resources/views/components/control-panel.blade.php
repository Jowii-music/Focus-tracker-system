@php
    $username = session('username', 'User');
    $activeSession = $activeSession ?? null;
@endphp

<style>

</style>

<section class="card control-card area-daily">
    <div class="control-top">
        <div class="card-title">Control panel</div>
        <div class="control-time" id="localTime">--:--</div>
    </div>

    <div class="control-greeting" id="controlGreeting">
        Good night, <span class="user-gradient">{{ $username }}</span>
    </div>

    <div class="control-tip" id="controlTip">
        Keep it light, calm, and focused.
    </div>

    <br><br>

    <div class="control-form">
        <div>
            <div class="field-label">Today's goal</div>
            <div class="goal-wrap">
                <input
                    type="text"
                    maxlength="100"
                    class="control-input goal-input"
                    id="sessionGoal"
                    placeholder="Example: Finish dashboard layout"
                    value="{{ $activeSession->goal ?? '' }}"
                >
                <span class="char-counter" id="charCounter">100 left</span>
            </div>
        </div>

        <div>
            <div class="field-label">Set time</div>

            <div class="time-grid">
                <input type="number" min="0" max="12" class="control-input" id="sessionHours" placeholder="Hours">
                <input type="number" min="0" max="59" class="control-input" id="sessionMinutes" placeholder="Minutes">

                <div class="select-wrap">
                    <select class="control-select" id="quickLength">
                        <option value="">Quick set</option>
                        <option value="15">15 min</option>
                        <option value="25">25 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">1 hour</option>
                        <option value="90">1h 30m</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="control-actions">
            <button class="control-btn start-btn" id="startPauseBtn" type="button">
                Start session
            </button>

            <button class="control-btn cancel-btn-main" id="cancelBtn" type="button">
                Cancel
            </button>
        </div>

        <button class="control-btn finish-btn" id="finishBtn" type="button">
            Finish session
        </button>

        <button class="control-btn reset-btn" id="resetBtn" type="button">
            Clear
        </button>

        <div class="error-message" id="controlError"></div>
    </div>
</section>

<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-title" id="confirmTitle">Confirm</div>
        <div class="confirm-text" id="confirmText">Are you sure?</div>

        <div class="confirm-actions">
            <button class="confirm-btn confirm-green" id="confirmYesBtn" type="button">
                Yes
            </button>

            <button class="confirm-btn confirm-no" id="confirmNoBtn" type="button">
                No
            </button>
        </div>
    </div>
</div>

<script>
const loggedInUserName = "{{ $username }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}";

let sessionActive = "{{ $activeSession ? 'yes' : 'no' }}" === "yes";
let sessionPaused = "{{ optional($activeSession)->status }}" === "paused";
let activeSessionId = "{{ $activeSession->id ?? '' }}";
let activeSessionTitle = "{{ $activeSession->title ?? '' }}";
let activeSessionStartedAt = "{{ $activeSession->started_at ?? '' }}";
let confirmAction = null;
let controlCountdownInterval = null;
let controlRemainingSeconds = 0;
let autoEnding = false;
let focusAchievedTimeout = null;
let focusAchievedSent = false;

const initialTargetSeconds = Number("{{ $activeSession->target_seconds ?? 0 }}");

const timeEl = document.getElementById("localTime");
const greetingEl = document.getElementById("controlGreeting");
const tipEl = document.getElementById("controlTip");

const goalInput = document.getElementById("sessionGoal");
const charCounter = document.getElementById("charCounter");
const hoursInput = document.getElementById("sessionHours");
const minutesInput = document.getElementById("sessionMinutes");
const quickLength = document.getElementById("quickLength");

const startPauseBtn = document.getElementById("startPauseBtn");
const cancelBtn = document.getElementById("cancelBtn");
const finishBtn = document.getElementById("finishBtn");
const resetBtn = document.getElementById("resetBtn");
const errorEl = document.getElementById("controlError");

const confirmOverlay = document.getElementById("confirmOverlay");
const confirmTitle = document.getElementById("confirmTitle");
const confirmText = document.getElementById("confirmText");
const confirmYesBtn = document.getElementById("confirmYesBtn");
const confirmNoBtn = document.getElementById("confirmNoBtn");

function getTargetSeconds() {
    return (((Number(hoursInput.value) || 0) * 60) + (Number(minutesInput.value) || 0)) * 60;
}

function fillTimeInputsFromSeconds(totalSeconds) {
    const totalMinutes = Math.floor(totalSeconds / 60);

    hoursInput.value = Math.floor(totalMinutes / 60);
    minutesInput.value = totalMinutes % 60;
}

function sendLiveUpdate(status, extra = {}) {
    window.dispatchEvent(new CustomEvent("focusSessionChanged", {
        detail: {
            status: status,
            title: activeSessionTitle,
            target_seconds: getTargetSeconds(),
            started_at: activeSessionStartedAt || new Date().toISOString(),
            remaining_seconds: controlRemainingSeconds,
            ...extra
        }
    }));
}

function updateClock() {
    const now = new Date();

    timeEl.textContent = now.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true
    });
}

function setGreeting() {
    const hour = new Date().getHours();

    let greeting = "Good night";
    let tip = "Keep it light, calm, and focused.";

    if (hour >= 5 && hour < 12) {
        greeting = "Good morning";
        tip = "Start with one clear goal before opening extra tabs.";
    } else if (hour >= 12 && hour < 18) {
        greeting = "Good day";
        tip = "Stay steady and finish one thing properly.";
    } else if (hour >= 18 && hour < 22) {
        greeting = "Good evening";
        tip = "Wrap up one useful thing before the day ends.";
    }

    greetingEl.innerHTML = `${greeting}, <span class="user-gradient">${loggedInUserName}</span>`;
    tipEl.textContent = tip;
}

function updateCharCounter() {
    charCounter.textContent = `${100 - goalInput.value.length} left`;
}

function calculateRemainingSecondsFromStart() {
    if (!activeSessionStartedAt || initialTargetSeconds <= 0) {
        return getTargetSeconds();
    }

    const startTime = new Date(activeSessionStartedAt).getTime();
    const now = new Date().getTime();
    const elapsedSeconds = Math.floor((now - startTime) / 1000);

    return Math.max(0, initialTargetSeconds - elapsedSeconds);
}

function startFocusAchievedTimer() {
    clearTimeout(focusAchievedTimeout);

    if (!sessionActive || sessionPaused || focusAchievedSent) return;

    focusAchievedTimeout = setTimeout(async function () {
        alert("Focus timer fired");
        
        if (!sessionActive || sessionPaused || focusAchievedSent) return;

        const response = await fetch("/session/focus-achieved", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({
                session_id: activeSessionId
            })
        });

        const data = await response.json();

        if (!data.success) return;

        focusAchievedSent = true;

        sendLiveUpdate("focused", {
            remaining_seconds: controlRemainingSeconds
        });

        if (typeof refreshLatestNotifications === "function") {
            refreshLatestNotifications();
        }
   }, 10000); // 10 seconds
}

function stopFocusAchievedTimer() {
    clearTimeout(focusAchievedTimeout);
}

function startControlCountdown() {
    clearInterval(controlCountdownInterval);

    controlCountdownInterval = setInterval(function () {
        if (!sessionActive || sessionPaused || autoEnding) return;

        controlRemainingSeconds--;

        if (controlRemainingSeconds <= 0) {
            controlRemainingSeconds = 0;
            autoEnding = true;
            endSession("finish", true);
        }
    }, 1000);
}

function stopControlCountdown() {
    clearInterval(controlCountdownInterval);
}

function lockInputs(locked) {
    goalInput.disabled = locked;
    hoursInput.disabled = locked;
    minutesInput.disabled = locked;
    quickLength.disabled = locked;
    resetBtn.disabled = locked;
}

function showError(message) {
    errorEl.textContent = message;
}

function clearError() {
    errorEl.textContent = "";
}

function setButtonState(state) {
    startPauseBtn.classList.remove("start-btn", "pause-btn", "resume-btn");

    if (state === "start") {
        startPauseBtn.textContent = "Start session";
        startPauseBtn.classList.add("start-btn");
    }

    if (state === "pause") {
        startPauseBtn.textContent = "Pause session";
        startPauseBtn.classList.add("pause-btn");
    }

    if (state === "resume") {
        startPauseBtn.textContent = "Resume session";
        startPauseBtn.classList.add("resume-btn");
    }
}

function showSessionButtons(show) {
    cancelBtn.classList.toggle("visible", show);
    finishBtn.classList.toggle("visible", show);
}

function openConfirm(type) {
    confirmAction = type;

    confirmYesBtn.classList.remove("confirm-green", "confirm-red");

    if (type === "finish") {
        confirmTitle.textContent = "Finish session?";
        confirmText.textContent = "This will finish the session and save the result.";
        confirmYesBtn.textContent = "Finish";
        confirmYesBtn.classList.add("confirm-green");
    }

    if (type === "cancel") {
        confirmTitle.textContent = "Cancel session?";
        confirmText.textContent = "This will cancel the session. It will not count as completed.";
        confirmYesBtn.textContent = "Cancel";
        confirmYesBtn.classList.add("confirm-red");
    }

    confirmOverlay.classList.add("show");
}

function closeConfirm() {
    confirmOverlay.classList.remove("show");
    confirmAction = null;
}

function resetControlPanel() {
    activeSessionId = null;
    activeSessionTitle = "";
    activeSessionStartedAt = "";
    sessionActive = false;
    sessionPaused = false;
    autoEnding = false;
    focusAchievedSent = false;

    stopFocusAchievedTimer();
    stopControlCountdown();
    lockInputs(false);
    setButtonState("start");
    showSessionButtons(false);
}

async function startSession() {
    const goal = goalInput.value.trim();
    const targetSeconds = getTargetSeconds();

    clearError();

    if (!goal) {
        showError("Please enter a session goal.");
        return;
    }

    if (targetSeconds <= 0) {
        showError("Please choose a session time.");
        return;
    }

    const response = await fetch("/session/start", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            goal: goal,
            target_seconds: targetSeconds
        })
    });

    const data = await response.json();

    if (!data.success) {
        showError(data.message || "Could not start session.");
        return;
    }

    activeSessionId = data.session_id;
    activeSessionTitle = data.title || goal;
    activeSessionStartedAt = data.started_at || new Date().toISOString();
    controlRemainingSeconds = targetSeconds;

    sessionActive = true;
    sessionPaused = false;
    autoEnding = false;
    focusAchievedSent = false;

    lockInputs(true);
    setButtonState("pause");
    showSessionButtons(true);
    startControlCountdown();
    startFocusAchievedTimer();

    sendLiveUpdate("active", {
        target_seconds: targetSeconds
    });
}

async function pauseOrResumeSession() {
    clearError();

    const wasPaused = sessionPaused;
    const frozenSeconds = controlRemainingSeconds;

    const response = await fetch(wasPaused ? "/session/resume" : "/session/pause", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            session_id: activeSessionId
        })
    });

    const data = await response.json();

    if (!data.success) {
        showError(data.message || "Could not update session.");
        return;
    }

    controlRemainingSeconds = frozenSeconds;
    sessionPaused = !sessionPaused;

    if (sessionPaused) {
        stopFocusAchievedTimer();
        focusAchievedSent = false;
    } else {
        focusAchievedSent = false;
        startFocusAchievedTimer();
    }

    setButtonState(sessionPaused ? "resume" : "pause");

    sendLiveUpdate(sessionPaused ? "paused" : "active", {
        remaining_seconds: controlRemainingSeconds
    });
}

async function endSession(type) {
    clearError();
    stopFocusAchievedTimer();

    const response = await fetch("/session/end", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            session_id: activeSessionId,
            end_type: type,
            remaining_seconds: controlRemainingSeconds
        })
    });

    const data = await response.json();

    if (!data.success) {
        showError(data.message || "Could not end session.");
        closeConfirm();
        autoEnding = false;
        return;
    }

    sendLiveUpdate(type === "cancel" ? "cancelled" : "completed", {
        result: data.result || (type === "cancel" ? "cancelled" : "completed"),
        title: data.title || activeSessionTitle,
        target_seconds: data.target_seconds || getTargetSeconds(),
        actual_seconds: data.actual_seconds || 0,
        remaining_seconds: controlRemainingSeconds
    });

    resetControlPanel();
    closeConfirm();
}

quickLength.addEventListener("change", function () {
    const totalMinutes = Number(quickLength.value);
    if (!totalMinutes) return;

    fillTimeInputsFromSeconds(totalMinutes * 60);
});

hoursInput.addEventListener("input", function () {
    quickLength.value = "";
});

minutesInput.addEventListener("input", function () {
    quickLength.value = "";
});

goalInput.addEventListener("input", updateCharCounter);

resetBtn.addEventListener("click", function () {
    if (sessionActive) return;

    goalInput.value = "";
    hoursInput.value = "";
    minutesInput.value = "";
    quickLength.value = "";
    updateCharCounter();
    clearError();
});

startPauseBtn.addEventListener("click", function () {
    if (!sessionActive) {
        startSession();
    } else {
        pauseOrResumeSession();
    }
});

cancelBtn.addEventListener("click", function () {
    openConfirm("cancel");
});

finishBtn.addEventListener("click", function () {
    openConfirm("finish");
});

confirmYesBtn.addEventListener("click", function () {
    if (confirmAction === "finish") {
        endSession("finish");
    }

    if (confirmAction === "cancel") {
        endSession("cancel");
    }
});

confirmNoBtn.addEventListener("click", closeConfirm);

if (initialTargetSeconds > 0) {
    fillTimeInputsFromSeconds(initialTargetSeconds);
}

if (sessionActive) {
    lockInputs(true);
    setButtonState(sessionPaused ? "resume" : "pause");
    showSessionButtons(true);

    controlRemainingSeconds = calculateRemainingSecondsFromStart();
    startControlCountdown();

    if (!sessionPaused) {
        startFocusAchievedTimer();
    }

    setTimeout(function () {
        sendLiveUpdate(sessionPaused ? "paused" : "active", {
            target_seconds: initialTargetSeconds,
            remaining_seconds: controlRemainingSeconds
        });
    }, 200);
} else {
    resetControlPanel();
}

setGreeting();
updateClock();
updateCharCounter();

setInterval(updateClock, 1000);
</script>