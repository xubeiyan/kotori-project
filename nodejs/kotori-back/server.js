const express = require('express');
const fileUpload = require('express-fileupload');
const cors = require('cors');
const bodyParser = require('body-parser');
const morgan = require('morgan');
const _ = require('lodash');

const router = require('./router');

// .env file
require('dotenv').config();

const app = express();

// 图片和略缩图文件
const imagePath = process.env.IMAGE_PATH || './uploads';
const thumbPath = process.env.THUMBNAIL_PATH || './thumbnails';
app.use('/images', express.static(imagePath));
app.use('/thumbs', express.static(thumbPath));

// enable files upload
app.use(fileUpload({
  createParentPath: true,
  safeFileNames: true,
  limits: {
    fileSize: 2 * 1024 * 1024
  }
}));

//add other middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

const morgan_log_type = process.env.PRODUCTION ? 'combined' : 'dev';
app.use(morgan(morgan_log_type));

// api路由处理api
app.use('/api', router);

//start app 
const port = process.env.PORT || 9000;

app.listen(port, () =>
  console.log(`[server.js] App is listening on port ${port}.`)
)