import { useContext } from 'react';
import ColorContext from '../context/colorContext.js';

import MenuItem from "./MenuItem";

import './Header.css';

function Header() {
  const { toggleText } = useContext(ColorContext);
  let colorScheme = toggleText == '🌞' ? 'color-light' : 'color-dark';
  let headerColor = `header ${colorScheme}`;
  return (
    <div className={headerColor}>
      <div className="container">
        <MenuItem url="/" text="首页"/>
      </div>
    </div>
  )
}

export default Header;