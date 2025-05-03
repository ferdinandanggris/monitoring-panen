const mqtt = require("mqtt");
const client = mqtt.connect("mqtt://broker.hivemq.com");

client.on("connect", () => {
  console.log("✅ Publisher connected to HiveMQ");

  const data = {
    machine_id: 1,
    latitude: -7.865417,
    longitude: 111.466117,
    speed: 8,
  };

  client.publish("sim800l/data", JSON.stringify(data), {}, () => {
    console.log("📤 Data published:", data);
    client.end();
  });
});
