import './PreviewDialog.css';

function PreviewDialog({ imgSrc, status, setPreview }) {
  return (
    <div
      className={status == 'hide' ? 'preview-background hide' : 'preview-background'}
      onClick={() => setPreview(preview => {return {...preview, status: 'hide'}})}> 
      <img src={imgSrc} alt="" />
      <div className='close-preview-button'>&#215;</div>
    </div>
  )
}

export default PreviewDialog;