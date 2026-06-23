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

  // ── Afleidende websites ──
  // Sociale media, video streaming, entertainment
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

  // Controleer op productieve site
  if (productiveKeywords.some(keyword => url.includes(keyword))) {
    return "productive";
  }

  // Controleer op afleidende site
  if (distractingKeywords.some(keyword => url.includes(keyword))) {
    return "distracted";
  }

  // Geen overeenkomst gevonden
  return "unknown";
}

let sessions =[]

/**
 * Sla een sessie op in chrome.storage.local
 * Sessies worden gegroepeerd per datum
 */
function saveSession(session) {
sessions.push(session)

console.log(sessions)

if(sessions.length == 10){
  sendBatch(sessions)

  sessions = []
}
}

function getDomain(rawUrl){
  try{
    const url = new URL(rawUrl.startsWith("http")? rawUrl: "https://" + rawUrl);
    return url.hostname.replace(/^www\./, "")
  }
  catch{
    return null
  }
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
    UserId: 1,
    BatchId: crypto.randomUUID(),
    Source: "extension",
    WindowTitle:           currentSession.titel,
    SiteUrl:         getDomain(currentSession.url),                              // naam van de pagina
    Category:        currentSession.category,                           // productief / afleidend / onbekend
    StartedAt:       currentSession.start.toISOString().slice(0, 19).replace("T", " "),                  // starttijd HH:MM:SS
    EndedAt:         end.toISOString().slice(0, 19).replace("T", " "),                                   // eindtijd HH:MM:SS
    DurationSeconds: Math.round((end - currentSession.start) / 1000),   // duur in seconden
    ActivityDate:            formatDate(currentSession.start),                   // datum YYYY-MM-DD
    
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
    titel:    tab.title,
    url: tab.url,         // paginatitel, of URL als fallback
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

async function sendBatch(batch) {
  try {
    const response = await fetch("http://localhost:3000/sessions/batch", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(batch)
    });

    if (!response.ok) {
      throw new Error(`Server error: ${response.status}`);
    }

    const data = await response.json();
    console.log("Batch sent successfully:", data);

    return data;
  } catch (err) {
    console.error("Failed to send batch:", err);
  }
}

