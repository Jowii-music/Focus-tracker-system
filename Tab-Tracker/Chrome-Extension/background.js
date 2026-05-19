import { saveSession } from './storage.js';

let currentTab = null;
let startTime = null;

// helper: pak domein naam als "naam"
function getSiteName(url) {
  try {
    const u = new URL(url);
    return u.hostname.replace("www.", "");
  } catch (e) {
    return "unknown";
  }
}

// tab switch detectie
chrome.tabs.onActivated.addListener(async (activeInfo) => {
  const tab = await chrome.tabs.get(activeInfo.tabId);

  handleTabChange(tab);
});

// URL change in zelfde tab
chrome.tabs.onUpdated.addListener((tabId, changeInfo, tab) => {
  if (changeInfo.status === "complete") {
    handleTabChange(tab);
  }
});

function handleTabChange(tab) {
  if (!tab.url) return;

  const now = Date.now();

  // oude sessie afsluiten
  if (currentTab && startTime) {
    const duration = now - startTime;

    saveSession({
      name: getSiteName(currentTab.url),
      url: currentTab.url,
      start: startTime,
      end: now,
      duration: duration
    });
  }

  // nieuwe sessie starten
  currentTab = tab;
  startTime = now;

  console.log("Tracking:", getSiteName(tab.url));
}