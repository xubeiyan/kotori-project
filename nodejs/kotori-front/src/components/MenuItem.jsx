import './MenuItem.css';
import { Link } from 'react-router-dom'

function MenuItem({ text, url }) {
  return (
      <Link to={url} >
        <span className="menu-item">
          {text}
        </span>
      </Link>
  )
}

export default MenuItem;