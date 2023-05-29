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
      <div className="container vertical-center">
        <MenuItem url="/" text="首页"/>
        <MenuItem url="/upload" text="上传" />
        <MenuItem url="/view" text="查看" />
      </div>
    </div>
  )
}

export default Header;