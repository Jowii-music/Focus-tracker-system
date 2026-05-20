const express = require("express");
const app = express();
const db = require("./services/db");

app.use(express.json());

const PORT = 3000;

const { insertActivityBatch } = require("./services/activityService");

// test route
app.get("/", (req, res) => {
    res.send("Focus server is running");
});

// receive batch from console app
app.post("/sessions/batch", async (req, res) => {
    
    try{
        const batch = req.body;
        const inserted = await insertActivityBatch(batch)

        res.json({
            status: "OK",
            inserted
        })
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
    
}


app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});