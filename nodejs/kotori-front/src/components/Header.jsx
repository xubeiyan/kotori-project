import MenuItem from "./MenuItem";

import './Header.css';

function Header({color}) {
  let colorScheme = color == 'light' ? 'color-light' : 'color-dark';
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