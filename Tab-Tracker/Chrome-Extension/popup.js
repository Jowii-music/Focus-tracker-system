document.getElementById('date-display').textContent =
  new Date().toLocaleDateString('nl-NL', { weekday:'short', day:'numeric', month:'long' });

function formatDuration(sec) {
  if (sec >= 3600) return Math.floor(sec/3600) + 'u ' + Math.floor((sec%3600)/60) + 'm';
  if (sec >= 60)   return Math.floor(sec/60) + 'm ' + (sec%60) + 's';
  return sec + 's';
}

function getDomain(url) {
  try { return new URL(url).hostname.replace('www.',''); } catch(e) { return url || '–'; }
}

function getCategory(url) {
  if (!url) return 'unknown';
  var p = ['github','gitlab','stackoverflow','stackexchange','docs.','developer.',
           'learn.','coursera','udemy','edx','notion','linear','figma','trello','jira',
           'asana','clickup','google.com/docs','google.com/sheets','office.com',
           'teams.microsoft','leetcode','codepen','replit','vercel','netlify','claude.ai','chat.openai'];
  var d = ['youtube.com','netflix.com','instagram.com','facebook.com','twitter.com',
           'x.com','tiktok.com','twitch.tv','snapchat.com','pinterest.com',
           'reddit.com','tumblr.com','9gag.com','discord.com'];
  if (p.some(function(k){ return url.indexOf(k) !== -1; })) return 'productive';
  if (d.some(function(k){ return url.indexOf(k) !== -1; })) return 'distracting';
  return 'unknown';
}

function renderTab(tab) {
  var url   = (tab && tab.url)   ? tab.url   : '';
  var title = (tab && tab.title) ? tab.title : (url || 'Onbekende pagina');
  var cat   = getCategory(url);

  var card = document.getElementById('status-card');
  card.className = 'status-card ' + cat;

  var labels = { productive:'✓ Productief', distracting:'✗ Afleidend', unknown:'? Onbekend' };
  document.getElementById('status-label').textContent = labels[cat];
  document.getElementById('page-title').textContent   = title;
  document.getElementById('page-domain').textContent  = getDomain(url);
}

function renderStorage(sessions) {
  var today    = new Date();
  var todayStr = today.toISOString().split('T')[0];
  var list     = (sessions[todayStr] && sessions[todayStr].sessions) ? sessions[todayStr].sessions : [];

  document.getElementById('sessions-today').textContent = list.length;

  var last = list[list.length - 1];
  var sec  = 0;
  if (last && last.startTime) {
    try {
      var parts = last.startTime.split(':');
      var start = new Date();
      start.setHours(parseInt(parts[0]), parseInt(parts[1]), parseInt(parts[2]), 0);
      sec = Math.max(0, Math.round((today - start) / 1000));
    } catch(e) { sec = 0; }
  }
  document.getElementById('session-duration').textContent = formatDuration(sec);

  var totalSec = list.reduce(function(s,x){ return s + (x.durationSeconds||0); }, 0);
  var prodSec  = list.filter(function(x){ return x.category==='productive'; })
                     .reduce(function(s,x){ return s + (x.durationSeconds||0); }, 0);
  var pct = totalSec > 0 ? Math.round(prodSec / totalSec * 100) : 0;
  document.getElementById('productive-pct').textContent = pct + '%';
  document.getElementById('progress-fill').style.width  = pct + '%';
}

chrome.tabs.query({ active: true, currentWindow: true }, function(tabs) {
  renderTab(tabs ? tabs[0] : null);
  chrome.storage.local.get({ sessionsByDate: {} }, function(data) {
    renderStorage(data.sessionsByDate || {});
  });
});

document.getElementById('view-all-btn').addEventListener('click', function() {
  chrome.storage.local.get({ sessionsByDate: {} }, function(data) {
    var json = JSON.stringify(data.sessionsByDate, null, 2);
    var blob = new Blob([json], { type: 'application/json' });
    chrome.tabs.create({ url: URL.createObjectURL(blob) });
  });
});
