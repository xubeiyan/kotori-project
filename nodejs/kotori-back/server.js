const express = require('express');
const fileUpload = require('express-fileupload');
const cors = require('cors');
const bodyParser = require('body-parser');
const morgan = require('morgan');
const _ = require('lodash');

const { generateRandomFileName } = require('./utils');
const e = require('express');

const app = express();

// 静态文件
app.use('/uploads', express.static('image'));

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
app.use(morgan('dev'));

//start app 
const port = process.env.PORT || 8080;

app.post('/upload', async (req, res) => {
  try {
    if (!req.files) {
      res.send({
        status: false,
        message: 'No file uploaded'
      });
      return;
    }
    //Use the name of the input field (i.e. "avatar") to retrieve the uploaded file
    let avatar = req.files.image;
    let extname = avatar.mimetype;

    const allowFileType = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];

    if (!allowFileType.includes(extname)) {
      res.send({
        status: false,
        message: 'Not Support Type'
      });
      return;
    }

    let filename = generateRandomFileName();
    let ext = 'png';
    if (extname == 'image/jpeg') ext = 'jpg';
    else if (extname == 'image/webp') ext = 'webp';
    else if (extname == 'image/gif') ext = 'gif';

    //Use the mv() method to place the file in the upload directory (i.e. "uploads")
    avatar.mv(`./uploads/${filename}.${ext}`);

    //send response
    res.send({
      status: true,
      message: 'File is uploaded',
      data: {
        saveName: filename
      }
    });

  } catch (err) {
    res.status(500).send(err);
  }
});

app.listen(port, () =>
  console.log(`App is listening on port ${port}.`)
)