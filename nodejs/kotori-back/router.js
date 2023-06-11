const express = require('express');
const router = express.Router();
const sharp = require('sharp');

const { generateRandomFileName, getFormatDate, log } = require('./utils')

const { addImage, queryImages } = require('./database');
const { FILE_SIZE_LIMIT } = require('./config');

const scriptName = 'router.js';

// .env file
require('dotenv').config();

// 图片文件
const imagePath = process.env.IMAGE_PATH || './uploads';
const thumbPath = process.env.THUMBNAIL_PATH || './thumbnails';

// 输出一个时间
router.use((req, res, next) => {
  const timeStr = getFormatDate(new Date(), 'yyyy/mm/dd HH:ii:ss');
  log(`[${scriptName}] Time: ${timeStr}`);
  next();
});

// 上传文件
router.post('/upload', async (req, res) => {
  try {
    if (!req.files) {
      res.send({
        status: 'NO_FILE_ERROR',
        message: 'No file uploaded'
      });
      return;
    }

    //Use the name of the input field (i.e. "avatar") to retrieve the uploaded file
    let avatar = req.files.image;
    let extname = avatar.mimetype;

    // 判断图片类型
    const allowFileType = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];

    if (!allowFileType.includes(extname)) {
      res.send({
        status: 'NOT_SUPPORT_FILE_TYPE',
        message: 'upload image file must br PNG, JPEG, WEBP or GIF'
      });
      return;
    }

    // 判断图片大小
    const fileSize = avatar.size;
    if (fileSize > FILE_SIZE_LIMIT) {
      res.send({
        status: 'MAX_SIZE_EXCEED',
        message: `file size ${fileSize} exceed the limit ${FILE_SIZE_LIMIT}`,
      });
      return;
    }

    let filename = generateRandomFileName();
    let ext = 'png';
    if (extname == 'image/jpeg') ext = 'jpg';
    else if (extname == 'image/webp') ext = 'webp';
    else if (extname == 'image/gif') ext = 'gif';

    //Use the mv() method to place the file in the upload directory (i.e. "uploads")
    avatar.mv(`${imagePath}/${filename}.${ext}`);

    // 由于万恶的SQLite只支持类似ISO-8601格式的datetime string: %Y-%m-%d %H:%M:%S
    // 月和分和其他的语言有着巨大差异，其他大多数使用M代表月03，而m代表月份3，而使用分则用I和i
    // 为了避免硬编码日期格式带来的不确定性，upload_time存储使用unix TimeStamp
    const uploadTime = new Date().getTime();
    
    let { nsfw, uploader } = req.body;
    if (nsfw !== 'nsfw') {
      // 需要view中展示的将文件转换为略缩图
      sharp(avatar.data).resize(50).toFile(`${thumbPath}/${filename}.${ext}`);
      nsfw = 'safe';
    }
    if (uploader !== 0) {
      uploader = 0;
    }

    // 写入db
    addImage({
      filename,
      fileType: extname,
      fileSize,
      uploadTime,
      uploaderId: uploader,
      mark: nsfw,
    });

    //send response
    res.send({
      status: 'SUCCESS',
      message: 'File is uploaded',
      data: {
        saveName: `${filename}.${ext}`,
      }
    });

  } catch (err) {
    res.status(500).send(err);
  }
});

// 列出文件
router.get('/view', (req, res) => {
  let pageNum = 1;

  let parsePageNum = parseInt(req.query.p);
  if (typeof parsePageNum == 'number' && parsePageNum > 0) {
    pageNum = parsePageNum;
  }

  let pageSize = 20;
  let parsePageSize = parseInt(req.query.size);

  if (typeof parsePageSize == 'number' && parsePageSize > 0 & parsePageSize <= 20) {
    pageSize = parsePageSize;
  }

  const result = queryImages({ pageNum, pageSize });

  const extMap = {
    'image/png': '.png',
    'image/jpeg': '.jpg',
    'image/webp': '.webp',
    'image/gif': '.gif',
  }

  let data = result.imageData.map(image => ({
    url: `${image.filename}${extMap[image.filetype]}`,
    upload_time: getFormatDate(new Date(image.upload_time), 'yyyy年mm月dd日 HH:ii:ss'),
    uploader_id: image.uploader_id,
    likes: image.likes,
  }))

  res.send({
    status: 'SUCCESS',
    satistics: {
      counts: result.counts,
    },
    data,
  })
});

// 为文件加一个赞
router.post('/addLike', (req, res) => {

});


module.exports = router;
