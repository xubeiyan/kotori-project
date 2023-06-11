const Database = require("better-sqlite3");
const { log } = require('./utils');
// .env file
require('dotenv').config();

const filepath = process.env.DATABASE_PATH || './db/kotori.db';

const scriptName = 'db.js';


const createDbConnection = () => {
  const option = {
    fileMustExist: true,
    // 生产模式不输出SQL语句
    verbose: process.env.PRODUCTION ? null : console.log,
  }
  const db = new Database(filepath, option);

  log(`[${scriptName}] connection with sqlite has been established`);

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
    filename, filetype, upload_time, uploader_id, likes
    FROM 'images' WHERE mark = 'safe' ORDER BY upload_time DESC
    LIMIT   ? OFFSET                 ? `).bind([
    pageSize, (pageNum - 1) * pageSize
  ]);

  result = stmt.all();


  console.log(result)
  return {
    counts,
    imageData: result,
  }

}

module.exports = { addImage, queryImages };