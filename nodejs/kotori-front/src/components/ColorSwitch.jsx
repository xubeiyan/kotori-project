import { useState } from 'react';
import './ColorSwitch.css';

function ColorSwitch({default_color, alter_color}) {
  const [toggleText, setToggleText] = useState('亮')

  const handleChange = e => {
    if (e.target.checked) {
      setToggleText('暗');
      return;
    }
    setToggleText('亮')
  }

  const dark = {
    '--dark-color': '#666',
    '--light-color': '#DDD',
  }

  return (
    <div style={dark}>
      <input onChange={handleChange} id="switch1" type="checkbox" className='toggle' />
      <label htmlFor="switch1">{toggleText}</label>
    </div>
  )
}

export default ColorSwitch;