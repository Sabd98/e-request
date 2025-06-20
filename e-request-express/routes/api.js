const express = require("express");
const router = express.Router();
const authMiddleware = require("../middleware/authMiddleware");
const {
  getRequestStatus,
  getUserRequests,
} = require("../controllers/requestControllers");

router.get("/request-status/:id", authMiddleware, getRequestStatus);
router.get("/request-list", authMiddleware, getUserRequests);

module.exports = router;
