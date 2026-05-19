const express = require("express");
const app = express();

app.use(express.json());

const PORT = 3000;

// test route
app.get("/", (req, res) => {
    res.send("Focus server is running");
});

// receive batch from console app
app.post("/sessions/batch", (req, res) => {
    const batch = req.body;

    console.log("Received batch:");
    console.log(JSON.stringify(batch, null, 2));

    res.json({
        status: "ok",
        received: batch.length
    });
});

app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});