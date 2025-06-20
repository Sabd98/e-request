const jwt = require("jsonwebtoken");


const dummyPayload = {
  user_id: 1,
  role: "requestor",
  name: "Test User",
};

const token = jwt.sign(dummyPayload, "your_strong_secret_here", {
  expiresIn: "1h",
  issuer: "e-request-laravel",
});

console.log("TEST TOKEN:", token);

const verifyToken = (req, res, next) => {
  const authHeader = req.headers.authorization;

  if (!authHeader || !authHeader.startsWith("Bearer ")) {
    return res.status(401).json({ error: "Token tidak valid" });
  }

  const token = authHeader.split(" ")[1];

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET, {
      issuer: process.env.JWT_ISSUER,
    });
    req.user = decoded;
    next();
  } catch (err) {
    res.status(401).json({ error: "Token expired atau invalid" });
  }
};

module.exports = verifyToken;
