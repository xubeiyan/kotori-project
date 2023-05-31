import CloseButton from './CloseButton';
import './PreviewDialog.css';

function PreviewDialog({ imgSrc, status, setPreview }) {
  return (
    <div
      className={status == 'hide' ? 'preview-background hide' : 'preview-background'}
      onClick={() => setPreview(preview => {return {...preview, status: 'hide'}})}> 
      <img src={imgSrc} alt="" />
      <CloseButton />
    </div>
  )
}

export default PreviewDialog;