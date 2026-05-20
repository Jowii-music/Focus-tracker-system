const db = require("./db");

async function insertActivityBatch(batch) {
    const query = `
        INSERT INTO activity_logs (
            batch_id,
            userId,
            source,
            app_name,
            site_url,
            window_title,
            category,
            started_at,
            ended_at,
            duration_seconds,
            processed,
            activity_date,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    for (const item of batch) {
        await db.query(query, [
            item.BatchId,
            item.UserId,
            item.Source,
            item.AppName,
            item.SiteUrl,
            item.WindowTitle,
            item.Category,
            item.StartedAt,
            item.EndedAt,
            item.DurationSeconds,
            0,
            item.ActivityDate,
            item.CreatedAt
        ]);
    }

    return batch.length;
}

module.exports = {
    insertActivityBatch
};