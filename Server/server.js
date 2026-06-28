const express = require("express");
const app = express();
const db = require("./services/db");

app.use(express.json());

const PORT = 3000;

const { insertActivityBatch } = require("./services/activityService");
const { insertReport } = require("./services/reportService")

// test route
app.get("/", (req, res) => {
    res.send("Focus server is running");
});

// receive batch from console app
app.post("/sessions/batch", async (req, res) => {
    
    try{
        const batch = req.body;
        const batch_id = batch[0].BatchId;

        console.log("batch recived:")
        console.log(batch)
        const inserted = await insertActivityBatch(batch)

        res.json({
            status: "OK",
            inserted
        })

        generateReport(batch_id);

    }
    catch(err){
        console.error(err);
        res.status(500).json({error: "internal server error"})
    }

});

async function testDb() {
    try{
        const [rows] = await db.query("SELECT 1 + 1 AS result")
        console.log("db connected")
    }
    catch(err){
        console.error("db connection failed:", err)
    }
    
};

async function generateReport(batch_id) {
    const [rows] = await db.query(
       `SELECT * FROM activity_logs WHERE batch_id = "${batch_id}"`
    );

    console.log("logging reciced data for report generation")
    console.log([rows])

    if (!rows.length) {
        throw new Error("No activity found for batch");
    }

    const summary = {
        total: 0,

        category: {
            productive: 0,
            distracted: 0,
            neutral: 0,
            unknown: 0
        },

        source: 0,

        apps: {},
        sites: {},
        hourly: {},
        focus_switches: 0
    };

    let lastApp = null;

    let lastSite =null;

    for (const row of rows) {
        const app = row.app_name;
        const site = getDomain(row.site_url);
        const duration = row.duration_seconds || 0;
        const category = row.category || "unknown";
        const source = row.source

        const hour = new Date(row.started_at).getHours();

        const date = new Date(row.started_at);

        if (isNaN(date.getTime())) {
            console.log("BAD DATE:", row.started_at);
        }

        // total time
        summary.total += duration;

        // category breakdown
        summary.category[category] =
            (summary.category[category] || 0) + duration;

        // app usage
        summary.apps[app] = (summary.apps[app] || 0) + duration;

        // site usage (optional)
        if (site) {
            summary.sites[site] = (summary.sites[site] || 0) + duration;
        }

        // hourly distribution
        summary.hourly[hour] = (summary.hourly[hour] || 0) + duration;

        // focus switches (basic version)
        if (lastApp && lastApp !== app) {
            summary.focus_switches++;
        }
        lastApp = app;

        if (lastSite && lastSite !== site) {
            summary.focus_switches++;
        }
        lastSite = site;

        summary.source = source
    }

    // helper: top used app/site
    const getTop = (obj) =>
        Object.entries(obj).sort((a, b) => b[1] - a[1])[0]?.[0] || null;

    console.log('logging source info:')
    console.log(summary.source)

    const report = {
        userId: rows[0].userId,
        report_date: new Date(),
        source: summary.source,

        total_tracked_seconds: summary.total,

        productive_seconds: summary.category.productive,
        distracted_seconds: summary.category.distracted,
        neutral_seconds: summary.category.unknown,

        focus_switches: summary.focus_switches,

        most_used_app: getTop(summary.apps),
        most_used_site: getTop(summary.sites),

        // flexible layers (your JSON columns)
        app_usage_json: summary.apps,
        category_usage_json: summary.category,
        hourly_distribution_json: summary.hourly
    };

    if(insertReport(report)){
        console.log('report inserted')
    }
    else{
        console.log('something went wrong')
    }

    console.log("report generated")
    console.log(report)
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

app.post("/post/notifications", async (req, res) => {
    console.log(req)


    const {
        UserId,
        header,
        content,
        category
    } = req.body;

    try {
        await db.query(
            `
            INSERT INTO dekstop_notifications
            (userId, header, content, category)
            VALUES (?, ?, ?, ?)
            `,
            [UserId, header, content, category]
        );

        res.status(201).json({
            success: true
        });
    }
    catch(err){
        console.error("notification creation failed:", err);

        res.status(500).json({
            error: "Internal server error"
        });
    }
});

app.get("/fetch/notifications", async (req, res) => {
    const userId = req.body.userId;
    const since = req.query.since;

    try {
        let query = `
            SELECT *
            FROM notifications
            WHERE user_id = ?
        `;

        const params = [userId];

        if (since) {
            query += ` AND created_at > ?`;
            params.push(since);
        }

        query += ` ORDER BY created_at ASC`;

        const [result] = await db.query(query, params);

        console.log("userId:", userId);
        console.log("since:", since);
        console.log("params:", params);
        console.log("query:", query);

        res.json({
            notifications: result,
            serverTime: new Date().toISOString()
        });

    } catch (err) {
        console.error("notification fetch failed:", err);
        res.status(500).json({ error: "Internal server error" });
    }
});
app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});