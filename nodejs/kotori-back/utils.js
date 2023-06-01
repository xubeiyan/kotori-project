const { v4: uuidv4 } = require('uuid');
const generateRandomFileName = () => {
  return uuidv4();
}

// 格式化日期
const getFormatDate = (date, format) => {
  const map = {
    mm: date.getMonth() + 1,
    dd: date.getDate(),
    yy: date.getFullYear().toString().slice(-2),
    yyyy: date.getFullYear(),
    HH: date.getHours(),
    hh: date.getHours() > 12 ? date.getHours() - 12 : date.getHours(),
    ii: date.getMinutes() > 10 ? date.getMinutes() : `0${date.getMinutes()}`,
    ss: date.getSeconds() > 10 ? date.getSeconds() : `0${date.getSeconds()}`,
  }

  return format.replace(/yyyy|yy|mm|dd|HH|hh|ii|ss/gi, matched => map[matched]);
}


// .env file
require('dotenv').config();
// 输出日志
const log = (text) => {
  if (process.env.PRODUCTION) {
    return;
  }

  console.log(text);
}

module.exports = { generateRandomFileName, getFormatDate, log }