const express = require('express');
const fileUpload = require('express-fileupload');
const cors = require('cors');
const bodyParser = require('body-parser');
const morgan = require('morgan');
const _ = require('lodash');
const path = require('path');

const router = require('./router');

// .env file
require('dotenv').config();

const app = express();

// 静态文件
app.use('/images', express.static(path.join(__dirname, 'uploads')));

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
const port = process.env.PORT || 8080;


app.listen(port, () =>
  console.log(`[server.js] App is listening on port ${port}.`)
)