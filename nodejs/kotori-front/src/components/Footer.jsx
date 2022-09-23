import './Footer.css';

import ColorSwitch from './ColorSwitch';

function Footer() {
  return (
    <div className="footer">
      <div className='container space-between'>
        <span>© 2021~2022 - Kotori Project</span>
        <ColorSwitch />
      </div>
    </div>
  )
}

export default Footer