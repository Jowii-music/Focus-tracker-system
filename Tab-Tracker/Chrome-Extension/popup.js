document.getElementById('date-display').textContent =
  new Date().toLocaleDateString('nl-NL', {
    weekday: 'short',
    day: 'numeric',
    month: 'long'
  });

  /**
 * LIVE UPDATE OF TAB INFO IN POPUP
 */
  window.addEventListener("load", () => {
  chrome.storage.local.get({ sessions: [] }, (data) => {
    renderStorage(data.sessions);
  });
});

/**
 * FORMAT DURATION (seconds → human readable)
 */
function formatDuration(sec) {
  if (!sec || sec <= 0) return '0s';
  if (sec >= 3600) return Math.floor(sec / 3600) + 'u ' + Math.floor((sec % 3600) / 60) + 'm';
  if (sec >= 60) return Math.floor(sec / 60) + 'm ' + (sec % 60) + 's';
  return sec + 's';
}
/**
 * FORMAT DATE (YYYY-MM-DD)
 */
function getDomain(url) {
  try {
    return new URL(url).hostname.replace('www.', '');
  } catch (e) {
    return url || '–';
  }
}
/**
 * FORMAT DATE (YYYY-MM-DD)
 */
function getCategory(url) {
  if (!url) return 'unknown';

  var p = [
    'github','gitlab','stackoverflow','stackexchange','docs.','developer.',
    'learn.','coursera','udemy','edx','notion','linear','figma','trello','jira',
    'asana','clickup','google.com/docs','google.com/sheets','office.com',
    'teams.microsoft','leetcode','codepen','replit','vercel','netlify',
    'claude.ai','chat.openai'
  ];

  var d = [
    'youtube.com','netflix.com','instagram.com','facebook.com','twitter.com',
    'x.com','tiktok.com','twitch.tv','snapchat.com','pinterest.com',
    'reddit.com','tumblr.com','9gag.com','discord.com'
  ];

  if (p.some(k => url.includes(k))) return 'productive';
  if (d.some(k => url.includes(k))) return 'distracting';

  return 'unknown';
}

/**
 * TAB INFO UI
 */
function renderTab(tab) {
  var url = tab?.url || '';
  var title = tab?.title || url || 'Onbekende pagina';
  var cat = getCategory(url);

  var card = document.getElementById('status-card');
  card.className = 'status-card ' + cat;

  var labels = {
    productive: '✓ Productief',
    distracting: '✗ Afleidend',
    unknown: '? Onbekend'
  };

  document.getElementById('status-label').textContent = labels[cat];
  document.getElementById('page-title').textContent = title;
  document.getElementById('page-domain').textContent = getDomain(url);
}

/**
 * STORAGE RENDER (FIXED FOR BACKGROUND.JS STRUCTURE)
 */
function renderStorage(list) {
  list = list || [];

  document.getElementById('sessions-today').textContent = list.length;

  // last session duration
  const last = list.at(-1);
  const lastSec = last?.DurationSeconds || 0;

  document.getElementById('session-duration').textContent =
    formatDuration(lastSec);

  // total time
  const totalSec = list.reduce((sum, x) => {
    return sum + (x.DurationSeconds || 0);
  }, 0);

  // productive time
  const prodSec = list
    .filter(x => x.Category === 'productive')
    .reduce((sum, x) => sum + (x.DurationSeconds || 0), 0);

  const pct = totalSec > 0
    ? Math.round((prodSec / totalSec) * 100)
    : 0;

  document.getElementById('productive-pct').textContent = pct + '%';
  document.getElementById('progress-fill').style.width = pct + '%';
}

/**
 * INIT
 */
chrome.tabs.query({ active: true, currentWindow: true }, function (tabs) {
  renderTab(tabs?.[0]);

  chrome.storage.local.get({ sessions: [] }, function (data) {
    renderStorage(data.sessions);
  });
});

/**
 * EXPORT BUTTON
 */
document.getElementById('view-all-btn').addEventListener('click', function () {
  chrome.storage.local.get({ sessions: [] }, function (data) {
    var json = JSON.stringify(data.sessions, null, 2);
    var blob = new Blob([json], { type: 'application/json' });

    var url = URL.createObjectURL(blob);

    chrome.tabs.create({ url: url }, function () {
      setTimeout(() => URL.revokeObjectURL(url), 10000);
    });
  });
});

/**
 * SEND BATCH BUTTON
 */
document.getElementById('sendBatchBtn').addEventListener('click', () => {
  chrome.runtime.sendMessage({ action: 'SEND_BATCH_NOW' });
});
/**
 *  live update of storage changes (sessions) in popup
 */
chrome.storage.onChanged.addListener((changes, area) => {
  if (area !== "local") return;

  if (changes.sessions) {
    const sessions = changes.sessions.newValue || [];
    renderStorage(sessions);
  }
});

/** 
 * live update of session duration in popup
 */
setInterval(() => {

  chrome.runtime.sendMessage(
    { action: "GET_CURRENT_SESSION" },
    (response) => {

      if (!response?.currentSession) return;

      const start = new Date(response.currentSession.start);
      const sec = Math.floor((Date.now() - start.getTime()) / 1000);

      document.getElementById("session-duration").textContent =
        formatDuration(sec);
    }
  );

}, 1000);