const Database = require('better-sqlite3');
const path = require('path');

const db_path = __dirname;
const db_name = process.argv[2] == undefined ? 'kotori.db' : process.argv[2];

const db = new Database(path.join(db_path, db_name), {
  fileMustExist: false,
});

try {
  const addImageTableStmt = db.prepare(`CREATE TABLE "images" (
    "id"	INTEGER UNIQUE,
    "filename"	TEXT NOT NULL,
    "filetype"	TEXT NOT NULL,
    "filesize"	INTEGER NOT NULL,
    "upload_time"	TEXT NOT NULL,
    "uploader_id"	INTEGER NOT NULL,
    PRIMARY KEY("id" AUTOINCREMENT)
  );`);
  
  addImageTableStmt.run();
  
  const addUserTableStmt = db.prepare(`CREATE TABLE "users" (
    "id"	INTEGER NOT NULL,
    "username"	TEXT NOT NULL,
    "password"	TEXT NOT NULL,
    "password_salt"	TEXT NOT NULL,
    "status"	TEXT NOT NULL DEFAULT 'enable',
    PRIMARY KEY("id" AUTOINCREMENT)
  );`)
  
  addUserTableStmt.run();
} catch(e) {
  console.error(e);
}
