console.log("Extension loaded");

let currentSession = null;

function formatDate(d) {
  return d.toISOString().split("T")[0];
}

function formatTime(d) {
  return d.toTimeString().split(" ")[0];
}

function getCategory(url) {
  if (!url) return "unknown";
  if (url.includes("github") || url.includes("code")) return "productive";
  return "unknown";
}

function saveSession(session) {
  const date = session.date;

  chrome.storage.local.get({ sessionsByDate: {} }, (data) => {
    const all = data.sessionsByDate;

    if (!all[date]) {
      all[date] = {
        date,
        sessions: []
      };
    }

    all[date].sessions.push(session);

    chrome.storage.local.set({ sessionsByDate: all }, () => {
      console.log("SESSION SAVED:", session);
      console.log("ALL DATA:", all);
    });
  });
}

async function endCurrentSession() {
  if (!currentSession) return;

  console.log("ENDING SESSION...");

  const end = new Date();

  const session = {
    app: currentSession.app,
    category: currentSession.category,
    startTime: formatTime(currentSession.start),
    endTime: formatTime(end),
    durationSeconds: Math.round((end - currentSession.start) / 1000),
    date: formatDate(currentSession.start)
  };

  saveSession(session);
}

function startSession(tab) {
  currentSession = {
    app: tab.title || tab.url,
    category: getCategory(tab.url),
    start: new Date()
  };

  console.log("NEW SESSION STARTED:", currentSession);
}

// TAB SWITCH
chrome.tabs.onActivated.addListener(async (activeInfo) => {
  const tab = await chrome.tabs.get(activeInfo.tabId);

  await endCurrentSession();
  startSession(tab);
});

// TAB UPDATED
chrome.tabs.onUpdated.addListener(async (tabId, changeInfo, tab) => {
  if (changeInfo.status === "complete") {
    const active = await chrome.tabs.query({ active: true, currentWindow: true });

    if (active[0]?.id === tabId) {
      await endCurrentSession();
      startSession(tab);
    }
  }
});

// INIT SESSION
chrome.tabs.query({ active: true, currentWindow: true }, (tabs) => {
  const tab = tabs[0];
  startSession(tab);
});