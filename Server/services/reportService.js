const db = require("./db");

async function insertReport(report) {
    const query = `
        INSERT INTO reports (
            userId,
            report_date,
            total_tracked_seconds,
            productive_seconds,
            distracted_seconds,
            neutral_seconds,
            focus_switches,
            most_used_app,
            most_used_site,
            app_useage_json,
            category_useage_json,
            hourly_distribution_json,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    await db.query(query, [
        report.userId,
        report.report_date,
        report.total_tracked_seconds,
        report.productive_seconds,
        report.distracted_seconds,
        report.neutral_seconds,
        report.focus_switches,
        report.most_used_app,
        report.most_used_site,
        JSON.stringify(report.app_usage_json),
        JSON.stringify(report.category_usage_json),
        JSON.stringify(report.hourly_distribution_json),
        new Date()
    ]);

    return 1;
}

module.exports = {
    insertReport
};