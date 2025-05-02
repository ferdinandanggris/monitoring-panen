const express = require('express');
const router = express.Router();
const controller = require('../controllers/trackingController');

router.post('/track', controller.track);

module.exports = router;
