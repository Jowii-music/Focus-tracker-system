console.log("Session Tracker geladen");

// ── Actieve sessie ──
let currentSession = null;

// ── In-memory cache (altijd sync met storage) ──
let sessions = [];

/**
 * Init load from storage
 */
chrome.storage.local.get(["sessions"], (data) => {
  sessions = data.sessions || [];
});

/**
 * Format helpers
 */
function formatDate(d) {
  return d.toISOString().split("T")[0];
}

/**
 * URL domain extractor
 */
function getDomain(rawUrl) {
  try {
    const url = new URL(rawUrl.startsWith("http") ? rawUrl : "https://" + rawUrl);
    return url.hostname.replace(/^www\./, "");
  } catch {
    return null;
  }
}

/**
 * Category detection
 */
function getCategory(url) {
  if (!url) return "unknown";

  const productiveKeywords = [
    "github", "gitlab", "bitbucket",
    "stackoverflow", "stackexchange",
    "code", "docs.", "developer.",
    "learn.", "coursera", "udemy", "edx",
    "notion", "linear", "figma",
    "trello", "jira", "asana", "clickup",
    "google.com/docs", "google.com/sheets",
    "office.com", "teams.microsoft",
    "leetcode", "codepen", "replit",
    "vercel", "netlify", "railway",
    "openai.com", "anthropic.com",
    "chat.openai", "claude.ai"
  ];

  const distractingKeywords = [
    "youtube.com", "netflix.com",
    "instagram.com", "facebook.com",
    "twitter.com", "x.com",
    "tiktok.com", "twitch.tv",
    "snapchat.com", "pinterest.com",
    "reddit.com", "tumblr.com",
    "9gag.com", "buzzfeed.com",
    "discord.com"
  ];

  if (productiveKeywords.some(k => url.includes(k))) return "productive";
  if (distractingKeywords.some(k => url.includes(k))) return "distracted";

  return "unknown";
}

/**
 * SAVE SESSION → FIXED (storage + memory sync)
 */
function saveSession(session) {
  sessions.push(session);

  chrome.storage.local.set({ sessions }, () => {
    console.log("Session saved:", session);
  });
}

/**
 * END SESSION → FIXED (0s filter + cleanup)
 */
async function endCurrentSession() {
  if (!currentSession) return;

  const end = new Date();
  const duration = end - currentSession.start;

  // FIX: skip ultra short sessions (tab flicker fix)
  if (duration < 1000) {
    currentSession = null;
    return;
  }

  const session = {
    UserId: 1,
    BatchId: crypto.randomUUID(),
    Source: "extension",
    WindowTitle: currentSession.titel,
    SiteUrl: getDomain(currentSession.url),
    Category: currentSession.category,
    StartedAt: currentSession.start.toISOString().slice(0, 19).replace("T", " "),
    EndedAt: end.toISOString().slice(0, 19).replace("T", " "),
    DurationSeconds: Math.round(duration / 1000),
    ActivityDate: formatDate(currentSession.start),
  };

  saveSession(session);

  currentSession = null;
}

/**
 * START SESSION
 */
function startSession(tab) {
  if (!tab?.url) return;

  currentSession = {
    titel: tab.title || "Unknown",
    url: tab.url,
    category: getCategory(tab.url),
    start: new Date()
  };

  console.log("Nieuwe sessie gestart:", currentSession);
}

/**
 * TAB SWITCH
 */
chrome.tabs.onActivated.addListener(async (activeInfo) => {
  const tab = await chrome.tabs.get(activeInfo.tabId);

  await endCurrentSession();
  startSession(tab);
});

/**
 * TAB UPDATE
 */
chrome.tabs.onUpdated.addListener(async (tabId, changeInfo, tab) => {
  if (changeInfo.status !== "complete") return;

  const active = await chrome.tabs.query({
    active: true,
    currentWindow: true
  });

  if (active[0]?.id === tabId) {
    await endCurrentSession();
    startSession(tab);
  }
});

/**
 * INIT
 */
chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
  const tab = tabs?.[0];
  if (tab?.url) startSession(tab);
});

/**
 * SEND BATCH
 */
async function sendBatch(batch) {

  console.log("Sending batch:", batch);

  try {
    const response = await fetch("http://localhost:3000/sessions/batch", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(batch)
    });

    if (!response.ok) throw new Error(`Server error: ${response.status}`);

    const data = await response.json();
    console.log("Batch sent:", data);

    return data;
  } catch (err) {
    console.error("Failed batch:", err);
  }
}

/**
 * MESSAGE HANDLER (popup)
 */
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.action === "SEND_BATCH_NOW") {

    if (!sessions.length) {
      sendResponse({ success: false, message: "No sessions" });
      return true;
    }

    sendBatch(sessions)
      .then(() => {
        sessions = [];
        chrome.storage.local.set({ sessions });

        sendResponse({ success: true, message: "Sent" });
      })
      .catch(err => {
        sendResponse({ success: false, message: err.message });
      });

    return true;
  }
});
/**
 * MESSAGE HANDLER (popup)
 */
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {

  if (message.action === "GET_CURRENT_SESSION") {
    sendResponse({
      currentSession
    });
    return true;
  }

});