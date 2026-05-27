// Laat in de console zien dat de extensie geladen is
console.log("Session Tracker geladen");

// ── Actieve sessie ──
// Houdt bij welke tab/app actief is en sinds wanneer
let currentSession = null;

/**
 * Zet een Date object om naar formaat YYYY-MM-DD
 * Voorbeeld: 2026-05-21
 */
function formatDate(d) {
  return d.toISOString().split("T")[0];
}

/**
 * Zet een Date object om naar formaat HH:MM:SS
 * Voorbeeld: 14:32:10
 */
function formatTime(d) {
  return d.toTimeString().split(" ")[0];
}

/**
 * Bepaal de categorie van een URL
 * - "productive"  : werk, code, leren
 * - "distracting" : sociale media, entertainment
 * - "unknown"     : alle andere sites
 */
function getCategory(url) {

  // Als er geen URL is, geef onbekend terug
  if (!url) return "unknown";

  // ── Productieve websites ──
  // Werk, code, documentatie, leerplatformen, projectbeheer
  const productiveKeywords = [
    "github", "gitlab", "bitbucket",          // versiebeheer
    "stackoverflow", "stackexchange",          // Q&A voor ontwikkelaars
    "code", "docs.", "developer.",             // documentatie
    "learn.", "coursera", "udemy", "edx",     // leerplatformen
    "notion", "linear", "figma",              // werktools
    "trello", "jira", "asana", "clickup",     // projectbeheer
    "google.com/docs", "google.com/sheets",   // Google Workspace
    "office.com", "teams.microsoft",          // Microsoft 365
    "leetcode", "codepen", "replit",          // code oefenen
    "vercel", "netlify", "railway",           // deployment
    "openai.com", "anthropic.com",            // AI tools
    "chat.openai", "claude.ai"               // AI chatbots (productief gebruik)
  ];

  // ── Afleidende websites ──
  // Sociale media, video streaming, entertainment
  const distractingKeywords = [
    "youtube.com", "netflix.com",            // video streaming
    "instagram.com", "facebook.com",         // sociale media
    "twitter.com", "x.com",                  // microblogging
    "tiktok.com", "twitch.tv",               // video/gaming
    "snapchat.com", "pinterest.com",         // visuele sociale media
    "reddit.com", "tumblr.com",              // forums/blogs
    "9gag.com", "buzzfeed.com",              // entertainment nieuws
    "discord.com"                            // gaming chat (kan ook werk zijn)
  ];

  // Controleer op productieve site
  if (productiveKeywords.some(keyword => url.includes(keyword))) {
    return "productive";
  }

  // Controleer op afleidende site
  if (distractingKeywords.some(keyword => url.includes(keyword))) {
    return "distracting";
  }

  // Geen overeenkomst gevonden
  return "unknown";
}

/**
 * Sla een sessie op in chrome.storage.local
 * Sessies worden gegroepeerd per datum
 */
function saveSession(session) {

  // Datum van de sessie (YYYY-MM-DD)
  const date = session.date;

  // Haal bestaande data op uit lokale opslag
  chrome.storage.local.get({ sessionsByDate: {} }, (data) => {

    // Alle opgeslagen sessies per datum
    const all = data.sessionsByDate;

    // Als er nog geen sessies zijn voor deze datum, maak een nieuwe dag aan
    if (!all[date]) {
      all[date] = {
        date,
        sessions: []
      };
    }

    // Voeg de huidige sessie toe aan de lijst van die dag
    all[date].sessions.push(session);

    // Sla de bijgewerkte data terug op
    chrome.storage.local.set({ sessionsByDate: all }, () => {

      // Debugging: toon de opgeslagen sessie in de console
      console.log("Sessie opgeslagen:", session);
      console.log("Alle data:", all);
    });
  });
}

/**
 * Beëindig de huidige sessie
 * Bereken de duur en sla de sessie op
 */
async function endCurrentSession() {

  // Stop als er geen actieve sessie loopt
  if (!currentSession) return;

  console.log("Sessie beëindigen...");

  // Noteer de eindtijd
  const end = new Date();

  // Bouw een sessie-object op met alle relevante informatie
  const session = {
    titel:           currentSession.titel,                              // naam van de pagina
    category:        currentSession.category,                           // productief / afleidend / onbekend
    startTime:       formatTime(currentSession.start),                  // starttijd HH:MM:SS
    endTime:         formatTime(end),                                   // eindtijd HH:MM:SS
    durationSeconds: Math.round((end - currentSession.start) / 1000),   // duur in seconden
    date:            formatDate(currentSession.start)                   // datum YYYY-MM-DD
  };

  // Sla de sessie op in lokale opslag
  saveSession(session);
}

/**
 * Start een nieuwe sessie voor de opgegeven tab
 * Slaat de starttijd op en bepaalt de categorie
 */
function startSession(tab) {

  // Maak een nieuw sessie-object aan voor de actieve tab
  currentSession = {
    titel:    tab.title || tab.url,         // paginatitel, of URL als fallback
    category: getCategory(tab.url),         // categorie op basis van URL
    start:    new Date()                    // starttijd van de sessie
  };

  console.log("Nieuwe sessie gestart:", currentSession);
}

/* =====================================================
   TAB SWITCH
   Wordt uitgevoerd wanneer de gebruiker van tab wisselt
===================================================== */

chrome.tabs.onActivated.addListener(async (activeInfo) => {

  // Haal de nieuwe actieve tab op via het tab-id
  const tab = await chrome.tabs.get(activeInfo.tabId);

  // Sluit de vorige sessie af
  await endCurrentSession();

  // Begin een nieuwe sessie voor de nieuwe tab
  startSession(tab);
});

/* =====================================================
   TAB UPDATED
   Wordt uitgevoerd wanneer een tab opnieuw wordt geladen
   of naar een andere URL navigeert
===================================================== */

chrome.tabs.onUpdated.addListener(async (tabId, changeInfo, tab) => {

  // Alleen verwerken als de pagina volledig is geladen
  if (changeInfo.status === "complete") {

    // Zoek de actieve tab in het huidige venster
    const active = await chrome.tabs.query({
      active: true,
      currentWindow: true
    });

    // Controleer of de bijgewerkte tab dezelfde is als de actieve tab
    if (active[0]?.id === tabId) {

      // Sluit de vorige sessie af
      await endCurrentSession();

      // Start een nieuwe sessie voor de vernieuwde pagina
      startSession(tab);
    }
  }
});

/* =====================================================
   INIT SESSION
   Start direct een sessie zodra de extensie wordt geladen
===================================================== */

chrome.tabs.query(
  { active: true, currentWindow: true },
  (tabs) => {

    // Eerste actieve tab bij het opstarten
    const tab = tabs[0];

    // Start meteen een sessie
    startSession(tab);
  }
);
fetch("http://localhost:3000/sessions/batch", {
  method: "POST",
  headers: {
    "Content-Type": "application/json"
  },
  body: JSON.stringify({           
    app: "YouTube",
    duration: 120,
    timestamp: new Date().toISOString()
  })
})
.then(response => response.json())
.then(data => {
  console.log("Succes:", data);
})
.catch(error => {
  console.error("Error:", error);
});