const { v4: uuidv4 } = require('uuid');
const generateRandomFileName = () => {
  return uuidv4();
}

// 格式化日期
const getFormatDate = (date, format) => {
  const map = {
    mm: () => date.getMonth() + 1,
    dd: () => date.getDate(),
    yy: () => date.getFullYear().toString().slice(-2),
    yyyy: () => date.getFullYear(),
    HH: () => date.getHours(),
    hh: () => { 
      const hour = date.getHours();
      return hour > 12 ? `${hour - 12}` : `${hour}`; 
    },
    ii: () => {
      const min = date.getMinutes();
      return min >= 10 ? `${min}` : `0${min}`
    },
    ss: () => {
      const sec = date.getSeconds();
      return sec >= 10 ? `${sec}` : `0${sec}`
    },
  }

  return format.replace(/yyyy|yy|mm|dd|HH|hh|ii|ss/gi, matched => map[matched]());
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