const Database = require('better-sqlite3');

const db = new Database('./kotori.db');

const stmt = db.prepare(`SELECT id, upload_time_1 FROM 'images'`);

const result = stmt.all();

result.forEach(one => {
  const timeStr = one.upload_time_1;
  const timeStamp = new Date(timeStr).getTime();
  const stmt = db.prepare('UPDATE images SET upload_time = ? WHERE id = ?').bind([timeStamp, one.id]);
  stmt.run();
})