import './ConfirmButton.css'

const ConfirmButton = ({ confirm, status, uploadFileCount }) => {
  let buttonContainerStyle = status == 'hide' ?
    'button-container hide' : 'button-container';

  return (
    <div className={buttonContainerStyle} >
      <button className="confirm-button"
        onClick={() => confirm()}>上传这<span className='upload-file-count'>{uploadFileCount}</span>张图片</button>
    </div>
  )
}

export default ConfirmButton;