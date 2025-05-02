const db = require('../db/knex');

const findOrCreateSession = async (machine_id, driver_id, latitude, longitude) => {
  let session = await db('session')
    .where({ machine_id, driver_id })
    .whereNull('end_time')
    .first();

  if (!session) {
    const [sessionId] = await db('session').insert({
      machine_id,
      driver_id,
      date: db.fn.now(),
      start_time: db.fn.now(),
      last_update_at: db.fn.now(),
      latitude: latitude,
      longitude: longitude
    });
    session = { id: sessionId };
  }

  return session;
};

const insertSessionDetail = async (session_id, data) => {
  // Get last sequence
  const last = await db('session_detail')
    .where({ session_id })
    .orderBy('sequence', 'desc')
    .first();

  const nextSequence = last ? last.sequence + 1 : 1;

  await db('session_detail').insert({
    session_id,
    sequence: nextSequence,
    latitude: data.latitude,
    longitude: data.longitude,
    speed: data.speed,
    recorded_at: db.fn.now()
  });

  await db('session')
    .where({ id: session_id })
    .update({ last_update_at: db.fn.now() });
};

module.exports = {
  findOrCreateSession,
  insertSessionDetail
};