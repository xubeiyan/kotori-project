import { useRef } from 'react';
import './CopyURLButton.css';

import LinkSVG from '../assets/Link';

function CopyURLButton({ url }) {
  const buttonRef = useRef(null);

  // 复制url
  const copyURL = () => {
    // 由于navigator.clipboard API要求使用https，但有时候又没有https
    // 所以只能回落到document.execCommand
    const fullURL = `${window.location.host}/images/${url}`;
    if (navigator.clipboard) {
      navigator.clipboard.writeText(fullURL);
      buttonRef.current.classList.add('copied');
    } else {
      const urlText = document.createElement('input');
      document.body.appendChild(urlText);
      urlText.value = fullURL;
      urlText.focus();
      urlText.select();
      const success = document.execCommand('copy');
      document.body.removeChild(urlText);
      if (success) {
        buttonRef.current.classList.add('copied');
      } else {
        buttonRef.current.classList.add('failed');
        console.log('看起来复制失败了，图片地址为：')
        console.log(fullURL);
      }
    }
  }

  return (
    <button ref={buttonRef} className='copy-url-button' onClick={copyURL}>
      <LinkSVG />
    </button>
  )
}

export default CopyURLButton