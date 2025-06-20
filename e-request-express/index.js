require("dotenv").config();
const express = require("express");
const cors = require("cors");
const app = express();
const apiRouter = require("./routes/api");
const db = require("./models");

app.use(cors());
app.use(express.json());
app.use("/api/v1", apiRouter);

app.get("/", (req, res) => {
  res.send("E-Request API Service");
});

db.sequelize
  .authenticate()
  .then(() => console.log("Database connected"))
  .catch((err) => console.error("Database connection error:", err));

// Sync model dengan database (dev only)
if (process.env.NODE_ENV === "development") {
  db.sequelize.sync(); // Gunakan { force: true } untuk reset database
}

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
