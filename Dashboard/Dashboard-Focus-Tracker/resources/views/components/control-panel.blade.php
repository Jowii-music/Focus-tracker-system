<style>
.control-card {
    grid-column: span 1;
    position: relative;
    overflow: hidden;
    min-height: 430px;
    padding: 26px;
}

.control-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background:
        radial-gradient(circle at top right, rgba(96,165,250,0.13), transparent 34%),
        radial-gradient(circle at bottom left, rgba(167,139,250,0.10), transparent 36%);
    pointer-events: none;
}

.control-card > * {
    position: relative;
    z-index: 1;
}

.control-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}

.control-time {
    font-size: 13px;
    font-weight: 900;
    padding: 8px 16px;
    border-radius: 999px;
    background: rgba(255,255,255,0.11);
}

.control-greeting {
    font-size: 25px;
    font-weight: 900;
    margin-bottom: 8px;
}

.user-gradient {
    background: linear-gradient(90deg, #a78bfa, #60a5fa, #34d399);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.control-tip {
    font-size: 14px;
    color: rgba(255,255,255,0.65);
    margin-bottom: 26px;
}

.control-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.field-label {
    font-size: 13px;
    font-weight: 800;
    color: rgba(255,255,255,0.82);
    margin-bottom: 8px;
}

.goal-wrap {
    position: relative;
}

.control-input,
.control-select {
    width: 100%;
    height: 44px;
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 14px;
    background: rgba(255,255,255,0.07);
    color: white;
    padding: 0 14px;
    outline: none;
    font-size: 14px;
}

.goal-input {
    padding-right: 82px;
}

.char-counter {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: rgba(255,255,255,0.45);
}

.control-input::placeholder {
    color: rgba(255,255,255,0.38);
}

.control-input:focus,
.control-select:focus {
    border-color: rgba(96,165,250,0.55);
    box-shadow: 0 0 0 3px rgba(96,165,250,0.10);
}

.control-input:disabled,
.control-select:disabled {
    opacity: 0.48;
    cursor: not-allowed;
}

.time-grid {
    display: grid;
    grid-template-columns: 100px 100px 1fr;
    gap: 12px;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    appearance: none;
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    appearance: textfield;
    -moz-appearance: textfield;
}

.select-wrap {
    position: relative;
}

.control-select {
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    padding-right: 44px;
}

.select-wrap::after {
    content: "";
    position: absolute;
    right: 18px;
    top: 50%;
    width: 9px;
    height: 9px;
    border-right: 2px solid rgba(255,255,255,0.85);
    border-bottom: 2px solid rgba(255,255,255,0.85);
    transform: translateY(-65%) rotate(45deg);
    pointer-events: none;
}

.control-select option {
    background: #2f2858;
    color: white;
}

.control-actions {
    display: flex;
    gap: 12px;
    margin-top: 10px;
}

.control-btn {
    flex: 1;
    height: 44px;
    border: none;
    border-radius: 14px;
    color: white;
    font-weight: 900;
    cursor: pointer;
    font-size: 15px;
}

.start-btn {
    background: linear-gradient(90deg, #34d399, #10b981);
}

.pause-btn {
    background: linear-gradient(90deg, #fb923c, #f59e0b);
}

.resume-btn {
    background: linear-gradient(90deg, #60a5fa, #3b82f6);
}

.stop-btn {
    display: none;
    background: linear-gradient(90deg, #f87171, #ef4444);
}

.stop-btn.visible {
    display: block;
}

.error-message {
    min-height: 18px;
    font-size: 13px;
    color: #f87171;
}

.control-footer {
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid rgba(255,255,255,0.10);
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.mini-info {
    padding: 14px;
    border-radius: 14px;
    background: rgba(255,255,255,0.045);
    border: 1px solid rgba(255,255,255,0.08);
}

.mini-info-title {
    font-size: 12px;
    font-weight: 900;
    margin-bottom: 6px;
}

.mini-info-text {
    font-size: 12px;
    color: rgba(255,255,255,0.58);
}
</style>

<section class="card control-card area-daily">
    <div class="control-top">
        <div class="card-title">Control panel</div>
        <div class="control-time" id="localTime">--:--</div>
    </div>

    <div class="control-greeting" id="controlGreeting">Good morning, <span class="user-gradient">User</span></div>
    <div class="control-tip" id="controlTip">Start with one clear goal.</div>

    <br>
    <br>
    
    <div class="control-form">
        <div>
            <div class="field-label">Today's goal</div>
            <div class="goal-wrap">
                <input
                    type="text"
                    maxlength="50"
                    class="control-input goal-input"
                    id="sessionGoal"
                    placeholder="Example: Finish dashboard layout"
                >
                <span class="char-counter" id="charCounter">50 left</span>
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
            <button class="control-btn start-btn" id="startPauseBtn" type="button">Start session</button>
            <button class="control-btn stop-btn" id="stopBtn" type="button">Stop session</button>
        </div>

        <div class="error-message" id="controlError"></div>
    </div>
</section>

<script>
const loggedInUserName = @json(session('username') ?? optional(auth()->user())->username ?? optional(auth()->user())->name ?? 'User');

const greetings = {
    morning: "Good morning",
    noon: "Good day",
    afternoon: "Good afternoon",
    evening: "Good evening",
    night: "Good night"
};

const tips = {
    morning: [
        "Pick one task and let the morning stay clean.",
        "Start with one clear goal before opening extra tabs.",
        "Begin with the task that matters most.",
        "Small progress early makes the rest of the day lighter.",
        "Keep your first session calm and focused."
    ],
    noon: [
        "Take a breath and choose what matters next.",
        "A small midday win can save the whole day.",
        "Reset your focus with one useful target.",
        "Pause, breathe, and continue with one clear task.",
        "Use this moment to get back on track."
    ],
    afternoon: [
        "Stay steady and finish one thing properly.",
        "Protect your focus from random distractions.",
        "A clear next step is better than a messy plan.",
        "Keep it simple and keep moving.",
        "One clean push can still change the day."
    ],
    evening: [
        "Wrap up one useful thing before the day ends.",
        "Finish gently, but finish something.",
        "A clean ending makes tomorrow easier.",
        "Slow focus still counts.",
        "Close the day with one clear win."
    ],
    night: [
        "Keep it light, calm, and focused.",
        "One small task is enough for tonight.",
        "Protect your rest while you work.",
        "Quiet focus. No pressure.",
        "Do not trade your peace for chaos."
    ]
};

let sessionActive = false;
let sessionPaused = false;

const timeEl = document.getElementById("localTime");
const greetingEl = document.getElementById("controlGreeting");
const tipEl = document.getElementById("controlTip");

const goalInput = document.getElementById("sessionGoal");
const charCounter = document.getElementById("charCounter");
const hoursInput = document.getElementById("sessionHours");
const minutesInput = document.getElementById("sessionMinutes");
const quickLength = document.getElementById("quickLength");

const startPauseBtn = document.getElementById("startPauseBtn");
const stopBtn = document.getElementById("stopBtn");
const errorEl = document.getElementById("controlError");

function randomItem(items) {
    return items[Math.floor(Math.random() * items.length)];
}

function getTimePeriod(hour) {
    if (hour >= 5 && hour < 12) return "morning";
    if (hour >= 12 && hour < 14) return "noon";
    if (hour >= 14 && hour < 18) return "afternoon";
    if (hour >= 18 && hour < 22) return "evening";
    return "night";
}

const currentPeriod = getTimePeriod(new Date().getHours());
const chosenTip = randomItem(tips[currentPeriod]);

function updateClock() {
    const now = new Date();

    timeEl.textContent = now.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true
    });
}

function setGreeting() {
    greetingEl.innerHTML = `${greetings[currentPeriod]}, <span class="user-gradient">${loggedInUserName}</span>`;
    tipEl.textContent = chosenTip;
}

function updateCharCounter() {
    const left = 50 - goalInput.value.length;
    charCounter.textContent = `${left} left`;
}

function getTotalMinutes() {
    const hours = Number(hoursInput.value) || 0;
    const minutes = Number(minutesInput.value) || 0;
    return (hours * 60) + minutes;
}

function lockInputs(locked) {
    goalInput.disabled = locked;
    hoursInput.disabled = locked;
    minutesInput.disabled = locked;
    quickLength.disabled = locked;
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

function startSession() {
    const goal = goalInput.value.trim();
    const totalMinutes = getTotalMinutes();

    clearError();

    if (!goal) {
        showError("Please enter a short session goal before starting.");
        goalInput.focus();
        return;
    }

    if (totalMinutes <= 0) {
        showError("Please enter hours, minutes, or choose a quick set time.");
        minutesInput.focus();
        return;
    }

    sessionActive = true;
    sessionPaused = false;

    lockInputs(true);
    setButtonState("pause");
    stopBtn.classList.add("visible");
}

function pauseSession() {
    sessionPaused = !sessionPaused;
    setButtonState(sessionPaused ? "resume" : "pause");
}

function stopSession() {
    sessionActive = false;
    sessionPaused = false;

    lockInputs(false);
    setButtonState("start");
    stopBtn.classList.remove("visible");
    clearError();
}

quickLength.addEventListener("change", () => {
    const total = Number(quickLength.value);

    if (!total) return;

    hoursInput.value = Math.floor(total / 60);
    minutesInput.value = total % 60;
});

goalInput.addEventListener("input", updateCharCounter);

startPauseBtn.addEventListener("click", () => {
    if (!sessionActive) {
        startSession();
    } else {
        pauseSession();
    }
});

stopBtn.addEventListener("click", stopSession);

setGreeting();
updateClock();
updateCharCounter();

setInterval(updateClock, 1000);
</script>