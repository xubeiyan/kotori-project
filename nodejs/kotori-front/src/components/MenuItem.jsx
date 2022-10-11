import './MenuItem.css';
import { Link, useLocation } from 'react-router-dom'

function MenuItem({ text, url }) {
  let { pathname } = useLocation();

  let current = pathname == url ? `current-path` : '';
  let style = `menu-item ${current}`
  return (
    <Link to={url} >
      <span className={style}>
        {text}
      </span>
    </Link>
  )
}

export default MenuItem;