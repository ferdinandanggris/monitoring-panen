const db = require("../db/knex");
const service = require("../services/trackingService");

exports.track = async (req, res, next) => {
  try {
    const { machine_id, latitude, longitude, speed, fuel_level } = req.body;

    if (!machine_id || !latitude || !longitude) {
      return res.status(400).json({
        status: false,
        message: "Missing required fields",
        error: [
          {
            field: "machine_id",
            message: "Machine ID is required",
          },
          {
            field: "latitude",
            message: "Latitude is required",
          },
          {
            field: "longitude",
            message: "Longitude is required",
          },
        ],
      });
    }

    const machine = await db("machine").where({ id: machine_id }).first();
    if (!machine) return res.status(404).json({ error: "Machine not found" });

    const driverId = machine.current_driver_id;
    if (!driverId) return res.status(400).json({ error: "No driver active" });

    const session = await service.findOrCreateSession(
      machine_id,
      driverId,
      latitude,
      longitude
    );
    await service.insertSessionDetail(session.id, {
      latitude,
      longitude,
      speed,
      fuel_level,
    });

    res.status(200).json({ message: "Tracking saved." });
  } catch (err) {
    next(err);
  }
};
