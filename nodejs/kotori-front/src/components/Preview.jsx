import './Preview.css';

function Preview({ imgSrc, status, setStatus }) {
  return (
    <div
      className={status == 'hide' ? 'preview-background hide' : 'preview-background'}
      onClick={() => setStatus('hide')}> 
      <img src={imgSrc} alt="" />
      <div className='close-preview-button'>&#215;</div>
    </div>
  )
}

export default Preview;