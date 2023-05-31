import { useRef } from 'react';
import './CopyURLButton.css';

import LinkSVG from '../assets/Link';

function CopyURLButton({ url }) {
  const buttonRef = useRef(null);

  // 复制url
  const copyURL = () => {
    buttonRef.current.classList.add('copied');
    const fullURL = `${window.location.host}/images/${url}`;
    navigator.clipboard.writeText(fullURL);
  }

  return (
    <button ref={buttonRef} className='copy-url-button' onClick={copyURL}>
      <LinkSVG />
    </button>
  )
}

export default CopyURLButton