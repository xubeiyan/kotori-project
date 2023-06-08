const sharp = require('sharp');
const fs = require('fs');

const imagesPath = '../uploads';
const thumbPath = '../thumbnails';

const files = fs.readdirSync(`${imagesPath}`);

files.forEach(file => {
  sharp(`${imagesPath}/${file}`).resize(50).toFile(`${thumbPath}/${file}`);
});