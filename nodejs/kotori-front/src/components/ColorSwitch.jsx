import { useContext } from 'react';
import './ColorSwitch.css';

import ColorContext from '../context/colorContext';

function ColorSwitch() {
  const { toggleText, setToggleText } = useContext(ColorContext);

  const handleChange = e => {
    if (e.target.checked) {
      setToggleText('🌙');
      localStorage.setItem('colorScheme', '🌙');
      return;
    }
    setToggleText('🌞');
    localStorage.setItem('colorScheme', '🌞');
  }

  const dark = {
    '--dark-color': '#666',
    '--light-color': '#DDD',
  }

  return (
    <div style={dark}>
      <input onChange={handleChange} id="switch1" type="checkbox" className='toggle' defaultChecked={toggleText == '🌙'}/>
      <label htmlFor="switch1">{toggleText}</label>
    </div>
  )
}

export default ColorSwitch;