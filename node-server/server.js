const express = require('express');
const mysql = require('mysql2/promise');
require('dotenv').config();

const app = express();
const PORT = 3000;

// Middleware untuk parsing JSON dan URL-encoded data
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Koneksi ke database
const db = mysql.createPool({
    host: process.env.DB_HOST || 'db', // 'db' nama service di docker-compose
    user: process.env.DB_USER || 'usermonitoring',
    password: process.env.DB_PASSWORD || 'secret123',
    database: process.env.DB_DATABASE || 'monitoring',
});

// Endpoint untuk menerima data dari SIM800L
app.post('/data', async (req, res) => {
    const { device_id, latitude, longitude, status } = req.body;

    if (!device_id || !latitude || !longitude) {
        return res.status(400).json({ message: 'Data tidak lengkap' });
    }

    try {
        // Cari ID mesin berdasarkan device_id
        const [mesin] = await db.query('SELECT id FROM mesin WHERE device_id = ?', [device_id]);

        if (mesin.length === 0) {
            return res.status(404).json({ message: 'Mesin tidak ditemukan' });
        }

        const mesinId = mesin[0].id;

        // Insert ke tabel tracking
        await db.query(
            'INSERT INTO tracking (mesin_id, latitude, longitude, status) VALUES (?, ?, ?, ?)',
            [mesinId, latitude, longitude, status || 'Aktif']
        );

        res.status(200).json({ message: 'Data berhasil disimpan' });
    } catch (error) {
        console.error('Error:', error);
        res.status(500).json({ message: 'Server Error' });
    }
});

// Test endpoint
app.get('/', (req, res) => {
    res.send('Server Node.js untuk SIM800L aktif!');
});

app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});