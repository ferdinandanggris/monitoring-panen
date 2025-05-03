const mqtt = require("mqtt");
const trackingController = require("./controllers/trackingController");

const fs = require("fs");
const path = require("path");
const logFile = path.join(__dirname, "mqtt.log");

// MQTT Setup
const mqttClient = mqtt.connect("mqtt://broker.hivemq.com");
mqttClient.on("connect", () => {
  console.log("📡 MQTT connected to HiveMQ");
  mqttClient.subscribe("sim800l/data", (err) => {
    if (!err) console.log("📥 Subscribed to sim800l/data");
  });
});

// MQTT Message Handler
mqttClient.on("message", async (topic, message) => {
  try {
    const parsed = JSON.parse(message.toString());
    const req = { body: parsed };
    const res = {
      status: (code) => ({
        json: (obj) => {
          if (code !== 200) {
            // Simpan log error saja
            const time = new Date().toISOString();
            const log = `[${time}] ERROR ${code} | Topic: ${topic} | Payload: ${message.toString()} | Response: ${JSON.stringify(
              obj
            )}\n`;
            fs.appendFileSync(logFile, log);
          }
          console.log(`[MQTT ${code}]`, obj);
        },
      }),
    };
    const next = (err) => {
      const time = new Date().toISOString();
      const log = `[${time}] EXCEPTION | Topic: ${topic} | Payload: ${message.toString()} | Error: ${
        err.message
      }\n`;
      fs.appendFileSync(logFile, log);
      console.error("❌ Controller error (MQTT):", err);
    };

    await trackingController.track(req, res, next);
  } catch (e) {
    const time = new Date().toISOString();
    const log = `[${time}] PARSE ERROR | Topic: ${topic} | Payload: ${message.toString()} | Error: ${
      e.message
    }\n`;
    fs.appendFileSync(logFile, log);
    console.error("❌ Invalid MQTT message:", message.toString());
  }
});
