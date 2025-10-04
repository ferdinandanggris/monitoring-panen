const mqtt = require("mqtt");
// Broker HiveMQ umum sering digunakan untuk pengujian
const client = mqtt.connect("mqtt://broker.hivemq.com");
const TOPIC = "sim800l/data";
const DELAY_MS = 1500; // Jeda 1.5 detik antar pengiriman

// Data simulasi pergerakan di sawah (36 titik)
const simulatedData = [
  // --- SEGMENT 1: Kerja Produktif (Maju, Speed Tinggi > 5.0 km/h) ---
  { "machine_id": 1, "latitude": -7.865000, "longitude": 111.466000, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.865100, "longitude": 111.466005, "speed": 9.8 },
  { "machine_id": 1, "latitude": -7.865200, "longitude": 111.466010, "speed": 10.0 },
  { "machine_id": 1, "latitude": -7.865300, "longitude": 111.466015, "speed": 9.7 },
  { "machine_id": 1, "latitude": -7.865400, "longitude": 111.466020, "speed": 9.5 },
  
  // --- SEGMENT 2: Putar Balik Lambat (Speed Rendah < 5.0 km/h - Harusnya difilter) ---
  { "machine_id": 1, "latitude": -7.865420, "longitude": 111.466025, "speed": 4.5 },
  { "machine_id": 1, "latitude": -7.865430, "longitude": 111.466030, "speed": 3.0 },
  { "machine_id": 1, "latitude": -7.865440, "longitude": 111.466035, "speed": 1.5 },
  { "machine_id": 1, "latitude": -7.865445, "longitude": 111.466040, "speed": 0.5 },
  { "machine_id": 1, "latitude": -7.865450, "longitude": 111.466045, "speed": 2.0 },
  
  // --- SEGMENT 3: Kerja Produktif (Kembali, Jalur Baru) ---
  { "machine_id": 1, "latitude": -7.865400, "longitude": 111.466055, "speed": 8.0 },
  { "machine_id": 1, "latitude": -7.865300, "longitude": 111.466060, "speed": 8.5 },
  { "machine_id": 1, "latitude": -7.865200, "longitude": 111.466065, "speed": 8.2 },
  { "machine_id": 1, "latitude": -7.865100, "longitude": 111.466070, "speed": 8.7 },
  { "machine_id": 1, "latitude": -7.865000, "longitude": 111.466075, "speed": 8.9 },

  // --- SEGMENT 4: Noise/Stuck (Titik Diam & Noise, Speed Rendah < 5.0 km/h - Harusnya difilter) ---
  { "machine_id": 1, "latitude": -7.865000, "longitude": 111.466075, "speed": 0.0 }, 
  { "machine_id": 1, "latitude": -7.865000, "longitude": 111.466075, "speed": 0.0 }, 
  { "machine_id": 1, "latitude": -7.865001, "longitude": 111.466075, "speed": 0.5 }, 
  { "machine_id": 1, "latitude": -7.865002, "longitude": 111.466076, "speed": 1.0 }, 
  { "machine_id": 1, "latitude": -7.865003, "longitude": 111.466077, "speed": 1.5 }, 

  // --- SEGMENT 5: Kerja Produktif (Jalur ke-3, Speed Tinggi) ---
  { "machine_id": 1, "latitude": -7.865050, "longitude": 111.466150, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.865150, "longitude": 111.466155, "speed": 9.3 },
  { "machine_id": 1, "latitude": -7.865250, "longitude": 111.466160, "speed": 9.1 },
  { "machine_id": 1, "latitude": -7.865350, "longitude": 111.466165, "speed": 9.4 },
  { "machine_id": 1, "latitude": -7.865450, "longitude": 111.466170, "speed": 9.6 },

  // --- SEGMENT 6: Melipat Area (Speed Tinggi, Uji Luas Poligon) ---
  { "machine_id": 1, "latitude": -7.865400, "longitude": 111.466130, "speed": 8.0 },
  { "machine_id": 1, "latitude": -7.865350, "longitude": 111.466100, "speed": 8.5 },
  { "machine_id": 1, "latitude": -7.865300, "longitude": 111.466070, "speed": 9.0 },
  { "machine_id": 1, "latitude": -7.865250, "longitude": 111.466040, "speed": 9.5 },
  { "machine_id": 1, "latitude": -7.865200, "longitude": 111.466010, "speed": 9.8 },

  // --- SEGMENT 7: Titik Akhir (Speed Tinggi) ---
  { "machine_id": 1, "latitude": -7.865100, "longitude": 111.465950, "speed": 10.0 },
  { "machine_id": 1, "latitude": -7.865000, "longitude": 111.465900, "speed": 10.0 },
  { "machine_id": 1, "latitude": -7.864900, "longitude": 111.465850, "speed": 10.0 },
  { "machine_id": 1, "latitude": -7.864800, "longitude": 111.465800, "speed": 10.0 },
  { "machine_id": 1, "latitude": -7.864700, "longitude": 111.465750, "speed": 10.0 }
];


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