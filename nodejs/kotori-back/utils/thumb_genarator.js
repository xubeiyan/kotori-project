/*
 *  由于某个版本之前只有原图没有略缩图，此工具即为通过原图生成略缩图的
 *  更改imagesPath和thumbPath的位置定位原图和略缩图的位置
 */

const sharp = require('sharp');
const fs = require('fs');

const imagesPath = '../uploads';
const thumbPath = '../thumbnails';

const files = fs.readdirSync(`${imagesPath}`);

files.forEach(file => {
  sharp(`${imagesPath}/${file}`).resize(50).toFile(`${thumbPath}/${file}`);
});