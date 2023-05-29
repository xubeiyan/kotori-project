const Database = require("better-sqlite3");
const filepath = './db/kotori.db';

const scriptName = 'db.js';

const createDbConnection = () => {
  const option = {
    fileMustExist: true,
    verbose: console.log,
  }
  const db = new Database(filepath, option);

  console.log(`[${scriptName}] connection with sqlite has been established`);

  return db;
}

const db = createDbConnection();

// 添加图像
const addImage = ({ filename, fileType, fileSize, uploadTime, uploaderId, mark }) => {
  const stmt = db.prepare(`INSERT INTO 'images' 
  (filename, filetype, filesize, upload_time, uploader_id, mark) VALUES
  (?       , ?       , ?       , ?          , ?          , ?)`);

  stmt.run([filename, fileType, fileSize, uploadTime, uploaderId, mark]);
}

// 获取指定页数的图像
const queryImages = ({ pageNum, pageSize }) => {
  let stmt = db.prepare(`SELECT COUNT(id) AS COUNTS FROM 'images'`);
  let result = stmt.get();
  const counts = result['COUNTS'];

  stmt = db.prepare(`SELECT 
    filename, filetype, upload_time, uploader_id
    FROM 'images' WHERE mark = 'safe' 
    LIMIT   ? OFFSET                 ? `).bind([
    pageSize, (pageNum - 1) * pageSize
  ]);

  result = stmt.all();

  return {
    counts,
    imageData: result,
  }

}

module.exports = { addImage, queryImages };