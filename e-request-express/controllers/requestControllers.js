const db = require("../models");

const getRequestStatus = async (req, res) => {
  try {
    const request = await db.Request.findByPk(req.params.id, {
      attributes: ["id", "title", "status", "created_at"],
      include: [
        {
          model: db.ApprovalLog,
          as: "approval_logs",
          attributes: ["action", "created_at"],
          order: [["created_at", "DESC"]],
          limit: 1,
        },
      ],
    });

    if (!request) {
      return res.status(404).json({ error: "Request tidak ditemukan" });
    }

    // Format response
    const response = {
      id: request.id,
      status: request.status,
      title: request.title,
      created_at: request.created_at,
      last_action: request.approval_logs[0]?.action || null,
      action_date: request.approval_logs[0]?.created_at || null,
    };

    res.json(response);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Server error" });
  }
};

const getUserRequests = async (req, res) => {
  try {
    // Validasi user_id
    if (!req.query.user_id) {
      return res.status(400).json({ error: "Parameter user_id diperlukan" });
    }

    const requests = await db.Request.findAll({
      where: {
        created_by: req.query.user_id,
        deleted_at: null,
      },
      attributes: ["id", "title", "request_type", "status", "created_at"],
      order: [["created_at", "DESC"]],
    });

    res.json(requests);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Server error" });
  }
};

module.exports = { getRequestStatus, getUserRequests };
