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
        <span>© 2021~2022 - Kotori Project</span>
        <ColorSwitch />
      </div>
    </div>
  )
}

export default Footer