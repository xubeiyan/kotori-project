const express = require('express');
const fileUpload = require('express-fileupload');
const cors = require('cors');
const bodyParser = require('body-parser');
const morgan = require('morgan');
const _ = require('lodash');
const path = require('path');

const { generateRandomFileName } = require('./utils');

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
}).get('/back/view', (req, res) => {
  console.log(`page is ${req.query.p}, size is ${req.query.size}`);
  res.send({
    status: true,
    data: [{
      url: 'http://localhost:8080/images/293debfb-562a-42ab-b03b-dcfea528b01e.jpg'
    }, {
      url: 'http://localhost:8080/images/3a760893-eb18-4064-a134-66374a4ab920.webp'
    }]
  })
});

app.listen(port, () =>
  console.log(`App is listening on port ${port}.`)
)