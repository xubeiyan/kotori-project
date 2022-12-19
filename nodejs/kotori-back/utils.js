const { v4: uuidv4 } = require('uuid');
const generateRandomFileName = () => {
  return uuidv4();
}

module.exports = { generateRandomFileName }