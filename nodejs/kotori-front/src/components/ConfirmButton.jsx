import './ConfirmButton.css'

const ConfirmButton = ({ confirm, status, uploadFileCount }) => {
  let buttonContainerStyle = status == 'hide' ?
    'button-container hide' : 'button-container';

  let uploadText = <span>上传这<span className="upload-file-count">{uploadFileCount}</span>张图片</span>

  return (
    <div className={buttonContainerStyle} >
      <button className="confirm-button"
        onClick={() => confirm()}
        disabled={status == 'uploading'}>{
          status == 'uploading' ? '上传中...' : uploadText
        }</button>
    </div>
  )
}

export default ConfirmButton;