require('dotenv').config();
const express = require('express');
const app = express();

const trackingRoutes = require('./routes/trackingRoutes');
const errorHandler = require('./middleware/errorHandler');
const { startWorker } = require('./workers/sessionAutoEndWorker');

app.use(express.json());
app.use('/api', trackingRoutes);
app.use(errorHandler);

// Start background worker
startWorker();

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Node server running on port ${PORT}`);
});