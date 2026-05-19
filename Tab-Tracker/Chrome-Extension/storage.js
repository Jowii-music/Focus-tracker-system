export function saveSession(session) {
  chrome.storage.local.get({ sessions: [] }, (data) => {
    const sessions = data.sessions;
    sessions.push(session);

    chrome.storage.local.set({ sessions });
  });
}

export function getSessions(callback) {
  chrome.storage.local.get({ sessions: [] }, (data) => {
    callback(data.sessions);
  });
}