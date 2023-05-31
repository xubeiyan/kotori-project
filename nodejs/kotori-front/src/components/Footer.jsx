import { useContext } from 'react';
import ColorContext from '../context/colorContext.js';

import './Footer.css';

import ColorSwitch from './ColorSwitch';

function Footer() {
  const { toggleText } = useContext(ColorContext);
  let colorScheme = toggleText == '🌞' ? 'color-light' : 'color-dark';
  let footerColor = `footer ${colorScheme}`;

  return (
    <div className={footerColor}>
      <div className='container space-between'>
        <span>© 2017~2023 - <a href='https://github.com/xubeiyan/kotori-project/tree/master/nodejs'>Kotori Project</a></span>
        <ColorSwitch />
      </div>
    </div>
  )
}

export default Footer