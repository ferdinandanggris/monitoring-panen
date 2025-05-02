const db = require('../db/knex');

const checkAndEndIdleSessions = async () => {
  const idleMinutes = process.env.IDLE_MINUTES || 5;
  const now = new Date();

  const sessions = await db('session')
    .whereNull('end_time')
    .whereNotNull('last_update_at');

  for (const session of sessions) {
    const last = new Date(session.last_update_at);
    const diff = (now - last) / 1000 / 60;
    if (diff >= idleMinutes) {
      console.log(`[Auto-End] Session ${session.id} ended after ${diff.toFixed(1)}m`);
      await db('session').where({ id: session.id }).update({ end_time: db.fn.now() });
    }
  }
};

const startWorker = () => {
  console.log('[Worker] Starting auto-end checker...');
  setInterval(checkAndEndIdleSessions, 60000);
};

module.exports = { startWorker };
