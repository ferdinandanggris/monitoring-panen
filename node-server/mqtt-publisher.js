const mqtt = require("mqtt");
// Broker HiveMQ umum sering digunakan untuk pengujian
const client = mqtt.connect("mqtt://broker.hivemq.com");
const TOPIC = "sim800l/data";
const DELAY_MS = 1500; // Jeda 1.5 detik antar pengiriman

// Data simulasi pergerakan di sawah (36 titik)
const simulatedData = [
  // --- SEGMENT 1: KERJA PRODUKTIF (MAJU LURUS, Speed > 5.0) ---
  { "machine_id": 1, "latitude": -7.860000, "longitude": 111.470000, "speed": 8.0 },
  { "machine_id": 1, "latitude": -7.859850, "longitude": 111.470005, "speed": 8.5 },
  { "machine_id": 1, "latitude": -7.859700, "longitude": 111.470010, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.859550, "longitude": 111.470015, "speed": 9.2 },
  { "machine_id": 1, "latitude": -7.859400, "longitude": 111.470020, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.859250, "longitude": 111.470025, "speed": 9.8 },
  { "machine_id": 1, "latitude": -7.859100, "longitude": 111.470030, "speed": 9.7 },
  { "machine_id": 1, "latitude": -7.858950, "longitude": 111.470035, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.858800, "longitude": 111.470040, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.858650, "longitude": 111.470045, "speed": 8.5 }, // P10: Titik Akhir Segmen 1
  
  // --- SEGMENT 2: PUTAR BALIK (LAMBAT, Speed < 5.0 - HARUS DIHAPUS) ---
  { "machine_id": 1, "latitude": -7.858600, "longitude": 111.470055, "speed": 4.5 },
  { "machine_id": 1, "latitude": -7.858550, "longitude": 111.470065, "speed": 3.0 },
  { "machine_id": 1, "latitude": -7.858500, "longitude": 111.470075, "speed": 1.5 },
  { "machine_id": 1, "latitude": -7.858550, "longitude": 111.470085, "speed": 2.0 },
  { "machine_id": 1, "latitude": -7.858600, "longitude": 111.470095, "speed": 4.0 }, // P15: Titik Akhir Putar Balik
  
  // --- SEGMENT 3: KERJA PRODUKTIF (KEMBALI LURUS, Speed > 5.0) ---
  { "machine_id": 1, "latitude": -7.858800, "longitude": 111.470250, "speed": 8.0 }, // Bergeser ke jalur baru
  { "machine_id": 1, "latitude": -7.858950, "longitude": 111.470245, "speed": 8.5 },
  { "machine_id": 1, "latitude": -7.859100, "longitude": 111.470240, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.859250, "longitude": 111.470235, "speed": 9.2 },
  { "machine_id": 1, "latitude": -7.859400, "longitude": 111.470230, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.859550, "longitude": 111.470225, "speed": 9.8 },
  { "machine_id": 1, "latitude": -7.859700, "longitude": 111.470220, "speed": 9.7 },
  { "machine_id": 1, "latitude": -7.859850, "longitude": 111.470215, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.860000, "longitude": 111.470210, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.860150, "longitude": 111.470205, "speed": 8.5 }, // P25: Titik Akhir Segmen 3
  
  // --- SEGMENT 4: PUTAR BALIK (LAMBAT, Speed < 5.0 - HARUS DIHAPUS) ---
  { "machine_id": 1, "latitude": -7.860200, "longitude": 111.470195, "speed": 4.5 },
  { "machine_id": 1, "latitude": -7.860250, "longitude": 111.470185, "speed": 3.0 },
  { "machine_id": 1, "latitude": -7.860300, "longitude": 111.470175, "speed": 1.5 },
  { "machine_id": 1, "latitude": -7.860250, "longitude": 111.470165, "speed": 2.0 },
  { "machine_id": 1, "latitude": -7.860200, "longitude": 111.470155, "speed": 4.0 }, // P30: Titik Akhir Putar Balik
  
  // --- SEGMENT 5: KERJA PRODUKTIF (MAJU LURUS, Jalur Terakhir) ---
  { "machine_id": 1, "latitude": -7.860000, "longitude": 111.470350, "speed": 8.0 }, // Bergeser ke jalur terakhir
  { "machine_id": 1, "latitude": -7.859800, "longitude": 111.470355, "speed": 8.5 },
  { "machine_id": 1, "latitude": -7.859600, "longitude": 111.470360, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.859400, "longitude": 111.470365, "speed": 9.2 },
  { "machine_id": 1, "latitude": -7.859200, "longitude": 111.470370, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.859000, "longitude": 111.470375, "speed": 9.8 },
  { "machine_id": 1, "latitude": -7.858800, "longitude": 111.470380, "speed": 9.7 },
  { "machine_id": 1, "latitude": -7.858600, "longitude": 111.470385, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.858400, "longitude": 111.470390, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.858200, "longitude": 111.470395, "speed": 8.5 } // P40: Titik Akhir
]


client.on("connect", () => {
  console.log("✅ Publisher connected to HiveMQ");
  publishData(0);
});

client.on("error", (err) => {
    console.error("❌ MQTT Error:", err);
    client.end();
});

/**
 * Fungsi untuk mengirim data secara berurutan dengan jeda waktu.
 * @param {number} index - Index data yang akan dikirim saat ini.
 */
function publishData(index) {
  if (index >= simulatedData.length) {
    console.log(`\n🎉 Semua ${simulatedData.length} data simulasi selesai dikirim.`);
    client.end();
    return;
  }

  const data = simulatedData[index];
  const payload = JSON.stringify(data);

  client.publish(TOPIC, payload, {}, (err) => {
    if (err) {
      console.error(`❌ Gagal mengirim data ke index ${index}:`, err);
    } else {
      console.log(`[${index + 1}/${simulatedData.length}] 📤 Data published (Speed: ${data.speed} km/h): ${payload}`);
    }
  });

  // Panggil dirinya sendiri setelah jeda
  setTimeout(() => {
    publishData(index + 1);
  }, DELAY_MS);
}