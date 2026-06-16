<style>
.control-card {
    grid-column: span 1;
    position: relative;
    overflow: hidden;
}

.control-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at top right, rgba(52, 211, 153, 0.13), transparent 30%),
        radial-gradient(circle at bottom left, rgba(96, 165, 250, 0.11), transparent 32%);
    pointer-events: none;
}

.control-card > * {
    position: relative;
    z-index: 1;
}

.control-head {
    margin-bottom: 14px;
}

.control-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.card-title {
    margin: 0;
}

.control-time {
    font-size: 12px;
    font-weight: 800;
    color: rgba(255,255,255,0.7);
    padding: 5px 9px;
    border-radius: 999px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.08);
}

.control-greeting {
    font-size: 17px;
    font-weight: 900;
    color: #ffffff;
    margin-bottom: 5px;
}

.control-tip {
    font-size: 12px;
    line-height: 1.45;
    color: rgba(255,255,255,0.58);
    max-width: 92%;
}

.session-panel {
    padding: 12px;
    border-radius: 16px;
    background: rgba(255,255,255,0.055);
    border: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 12px;
}

.session-status-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.session-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
    color: #34d399;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #34d399;
    box-shadow: 0 0 12px #34d399;
}

.session-state.paused {
    color: #60a5fa;
}

.session-state.paused .status-dot {
    background: #60a5fa;
    box-shadow: 0 0 12px #60a5fa;
}

.session-state.stopped {
    color: rgba(255,255,255,0.55);
}

.session-state.stopped .status-dot {
    background: rgba(255,255,255,0.35);
    box-shadow: none;
}

.session-count {
    font-size: 20px;
    font-weight: 900;
    color: #ffffff;
}

.goal-preview {
    font-size: 12px;
    color: rgba(255,255,255,0.58);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.goal-preview strong {
    color: #ffffff;
}

.control-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.input-row {
    display: grid;
    grid-template-columns: 1fr 110px;
    gap: 8px;
}

.goal-row {
    display: grid;
    grid-template-columns: 1fr 82px;
    gap: 8px;
}

.control-input,
.control-select {
    width: 100%;
    height: 34px;
    border: 1px solid rgba(255,255,255,0.1);
    outline: none;
    border-radius: 11px;
    background: rgba(255,255,255,0.065);
    color: #ffffff;
    padding: 0 11px;
    font-size: 12px;
}

.control-select {
    cursor: pointer;
}

.control-select option {
    background: #2f2857;
    color: #ffffff;
}

.control-input::placeholder {
    color: rgba(255,255,255,0.38);
}

.control-input:focus,
.control-select:focus {
    border-color: rgba(52, 211, 153, 0.42);
    box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.08);
}

.control-actions {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
}

.control-btn {
    height: 34px;
    border: none;
    border-radius: 11px;
    font-size: 12px;
    font-weight: 900;
    color: #ffffff;
    cursor: pointer;
    transition: 0.2s ease;
}

.control-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.08);
}

.start-btn {
    background: linear-gradient(90deg, #34d399, #10b981);
}

.pause-btn {
    background: linear-gradient(90deg, #60a5fa, #3b82f6);
}

.break-btn {
    background: linear-gradient(90deg, #fb923c, #f59e0b);
}

.goal-btn {
    height: 34px;
    border: none;
    border-radius: 11px;
    background: rgba(255,255,255,0.11);
    color: #ffffff;
    font-size: 12px;
    font-weight: 900;
    cursor: pointer;
}

.goal-btn:hover {
    background: rgba(255,255,255,0.16);
}

.micro-note {
    margin-top: 10px;
    font-size: 11px;
    color: rgba(255,255,255,0.42);
}

@media (max-width: 680px) {
    .input-row,
    .goal-row,
    .control-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="card control-card area-daily">

    <div class="control-head">
        <div class="control-title-row">
            <div class="card-title">Control panel</div>
            <div class="control-time" id="localTime">--:--</div>
        </div>

        <div class="control-greeting" id="controlGreeting">
            Welcome back.
        </div>

        <div class="control-tip" id="controlTip">
            Set your session and ease into focus.
        </div>
    </div>

    <div class="session-panel">
        <div class="session-status-row">
            <div class="session-status session-state stopped" id="sessionState">
                <span class="status-dot"></span>
                <span id="sessionStatusText">No active session</span>
            </div>

            <div class="session-count" id="sessionTimer">00:00</div>
        </div>

        <div class="goal-preview">
            Goal:
            <strong id="goalPreview">No goal set yet</strong>
        </div>
    </div>

    <div class="control-form">

        <div class="input-row">
            <input
                type="number"
                min="1"
                max="240"
                step="1"
                inputmode="numeric"
                class="control-input"
                id="sessionLength"
                placeholder="Session length in minutes"
            >

            <select class="control-select" id="quickLength">
                <option value="">Quick set</option>
                <option value="15">15 min</option>
                <option value="25">25 min</option>
                <option value="30">30 min</option>
                <option value="45">45 min</option>
                <option value="60">60 min</option>
                <option value="90">90 min</option>
            </select>
        </div>

        <div class="control-actions">
            <button class="control-btn start-btn" id="startStopBtn">
                Start session
            </button>

            <button class="control-btn pause-btn" id="pauseBtn">
                Pause
            </button>

            <button class="control-btn break-btn" id="breakBtn">
                Take break
            </button>
        </div>

        <div class="goal-row">
            <input
                type="text"
                maxlength="80"
                class="control-input"
                id="sessionGoal"
                placeholder="What is your goal for this session?"
            >

            <button class="goal-btn" id="goalBtn">
                Submit
            </button>
        </div>

    </div>

    <div class="micro-note" id="microNote">
        Tip: choose one clear goal before starting.
    </div>

</section>

<script>
const loggedInUserName = "{{ Auth::user()->name ?? 'Nero' }}";

const greetingData = {
    morning: [
        "Good morning",
        "Morning focus mode",
        "Fresh start today",
        "Rise and focus",
        "New day, new run"
    ],
    noon: [
        "Good noon",
        "Midday check-in",
        "Halfway through",
        "Noon focus window",
        "Keep the rhythm"
    ],
    afternoon: [
        "Good afternoon",
        "Afternoon focus time",
        "Steady pace now",
        "Back in the zone",
        "Power through softly"
    ],
    evening: [
        "Good evening",
        "Evening session ready",
        "Calm focus time",
        "Smooth evening grind",
        "Late-day momentum"
    ],
    night: [
        "Good night",
        "Late session detected",
        "Night focus mode",
        "Quiet hours activated",
        "Soft grind tonight"
    ]
};

const tipData = {
    morning: [
        "Start simple. One clean task is enough to build momentum.",
        "Drink some water, open your tools, and let the day begin gently.",
        "A calm first session can shape the whole day.",
        "Pick the easiest important task and warm up with that.",
        "Fresh brain detected. Use it before the distractions wake up.",
        "Start with structure, not pressure.",
        "Your first session does not need to be perfect. It just needs to begin.",
        "Clear desk, clear goal, clean start.",
        "The morning is best for building rhythm.",
        "Give your focus a soft landing before going full speed."
    ],
    noon: [
        "Midday reset. Check your goal before you continue.",
        "Noon is a good time to save progress and clean up loose tasks.",
        "Keep it steady. You do not need to rush to be productive.",
        "A small break now can protect the rest of the day.",
        "Half the day is gone, but the useful part is still yours.",
        "Review what worked this morning and repeat it.",
        "Choose one task. Close everything else.",
        "Your focus does not need more noise. It needs direction.",
        "A short session now can stop the afternoon from drifting.",
        "Good moment to refuel and return with intention."
    ],
    afternoon: [
        "Afternoon dip is normal. Make the next step small.",
        "Stay locked in, but keep it gentle.",
        "One focused block now can save your evening.",
        "Do not chase everything. Pick the next useful thing.",
        "Energy may drop, so let structure carry you.",
        "This is a good time for practical tasks and finishing touches.",
        "Protect your attention. The noisy tabs can wait.",
        "Small progress still counts, especially now.",
        "Set a timer and let the session do the thinking.",
        "The goal is not to feel perfect. The goal is to continue."
    ],
    evening: [
        "Evening focus works best when the goal is clear.",
        "Nice and calm. Finish one thing before opening another.",
        "Use this session to clean up, polish, or prepare tomorrow.",
        "You are allowed to slow down and still be productive.",
        "Good time for steady work, not chaotic multitasking.",
        "Keep the session realistic. Future you will appreciate it.",
        "Close the loop on one task before the day ends.",
        "Evening progress feels better when it is focused.",
        "Do not overload the night. Aim for clean progress.",
        "A calm finish beats a messy sprint."
    ],
    night: [
        "Late session detected. Stay sharp, but don’t forget to rest.",
        "Night focus can be powerful, but keep an eye on your energy.",
        "Quiet hours are good for deep work. Just do not disappear into it.",
        "Pick one goal only. Night sessions punish multitasking.",
        "If your eyes feel heavy, make this a short clean session.",
        "Soft focus mode. No panic, no rush.",
        "Do the important bit, then let yourself rest.",
        "Late work counts, but sleep still matters.",
        "Keep it controlled. The night can steal time fast.",
        "One clean win, then recovery."
    ]
};

let sessionActive = false;
let sessionPaused = false;
let sessionSeconds = 0;
let sessionLimitSeconds = 0;
let sessionInterval = null;

const greetingEl = document.getElementById("controlGreeting");
const tipEl = document.getElementById("controlTip");
const timeEl = document.getElementById("localTime");
const sessionTimerEl = document.getElementById("sessionTimer");
const sessionStateEl = document.getElementById("sessionState");
const sessionStatusTextEl = document.getElementById("sessionStatusText");
const startStopBtn = document.getElementById("startStopBtn");
const pauseBtn = document.getElementById("pauseBtn");
const breakBtn = document.getElementById("breakBtn");
const sessionLengthInput = document.getElementById("sessionLength");
const quickLength = document.getElementById("quickLength");
const sessionGoalInput = document.getElementById("sessionGoal");
const goalBtn = document.getElementById("goalBtn");
const goalPreview = document.getElementById("goalPreview");
const microNote = document.getElementById("microNote");

function getTimePeriod(hour) {
    if (hour >= 5 && hour < 12) return "morning";
    if (hour >= 12 && hour < 14) return "noon";
    if (hour >= 14 && hour < 18) return "afternoon";
    if (hour >= 18 && hour < 22) return "evening";
    return "night";
}

function pickRandom(items) {
    return items[Math.floor(Math.random() * items.length)];
}

function updateGreeting() {
    const now = new Date();
    const hour = now.getHours();
    const period = getTimePeriod(hour);

    const time = now.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit"
    });

    timeEl.textContent = time;

    const greeting = pickRandom(greetingData[period]);
    const tip = pickRandom(tipData[period]);

    greetingEl.textContent = `${greeting}, ${loggedInUserName}.`;
    tipEl.textContent = tip;
}

function formatTimer(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;

    return `${String(minutes).padStart(2, "0")}:${String(remainingSeconds).padStart(2, "0")}`;
}

function updateTimerDisplay() {
    sessionTimerEl.textContent = formatTimer(sessionSeconds);
}

function setSessionState(state) {
    sessionStateEl.classList.remove("stopped", "paused");

    if (state === "running") {
        sessionStatusTextEl.textContent = "Session active";
        microNote.textContent = "Focus mode is on. Keep the goal small and clear.";
    }

    if (state === "paused") {
        sessionStateEl.classList.add("paused");
        sessionStatusTextEl.textContent = "Session paused";
        microNote.textContent = "Paused. Breathe, stretch, then come back clean.";
    }

    if (state === "stopped") {
        sessionStateEl.classList.add("stopped");
        sessionStatusTextEl.textContent = "No active session";
        microNote.textContent = "Tip: choose one clear goal before starting.";
    }
}

function startSession() {
    const minutes = Number(sessionLengthInput.value);

    if (!minutes || minutes < 1) {
        microNote.textContent = "Please enter a session length first.";
        sessionLengthInput.focus();
        return;
    }

    sessionActive = true;
    sessionPaused = false;
    sessionSeconds = 0;
    sessionLimitSeconds = minutes * 60;

    startStopBtn.textContent = "Stop session";
    pauseBtn.textContent = "Pause";

    setSessionState("running");
    updateTimerDisplay();

    clearInterval(sessionInterval);

    sessionInterval = setInterval(() => {
        if (!sessionActive || sessionPaused) return;

        sessionSeconds++;
        updateTimerDisplay();

        if (sessionSeconds >= sessionLimitSeconds) {
            stopSession();
            microNote.textContent = "Session complete. Nice work — take a small reset.";
        }
    }, 1000);
}

function stopSession() {
    sessionActive = false;
    sessionPaused = false;

    clearInterval(sessionInterval);

    startStopBtn.textContent = "Start session";
    pauseBtn.textContent = "Pause";

    setSessionState("stopped");
}

function togglePause() {
    if (!sessionActive) {
        microNote.textContent = "Start a session before pausing.";
        return;
    }

    sessionPaused = !sessionPaused;

    if (sessionPaused) {
        pauseBtn.textContent = "Resume";
        setSessionState("paused");
    } else {
        pauseBtn.textContent = "Pause";
        setSessionState("running");
    }
}

function takeBreak() {
    if (!sessionActive) {
        microNote.textContent = "Break noted. Start a session when you are ready.";
        return;
    }

    sessionPaused = true;
    pauseBtn.textContent = "Resume";
    setSessionState("paused");
    microNote.textContent = "Break started. Step away for a moment.";
}

function submitGoal() {
    const goal = sessionGoalInput.value.trim();

    if (!goal) {
        microNote.textContent = "Type a goal first. Make it short and clear.";
        sessionGoalInput.focus();
        return;
    }

    goalPreview.textContent = goal;
    microNote.textContent = "Goal saved. Keep the session aimed at that.";
    sessionGoalInput.value = "";
}

sessionLengthInput.addEventListener("input", () => {
    sessionLengthInput.value = sessionLengthInput.value.replace(/[^0-9]/g, "");
});

quickLength.addEventListener("change", () => {
    if (quickLength.value) {
        sessionLengthInput.value = quickLength.value;
    }
});

startStopBtn.addEventListener("click", () => {
    if (sessionActive) {
        stopSession();
    } else {
        startSession();
    }
});

pauseBtn.addEventListener("click", togglePause);
breakBtn.addEventListener("click", takeBreak);
goalBtn.addEventListener("click", submitGoal);

sessionGoalInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
        submitGoal();
    }
});

updateGreeting();
updateTimerDisplay();
setSessionState("stopped");

setInterval(() => {
    const oldMinute = timeEl.textContent;
    updateGreeting();
}, 60000);
</script>