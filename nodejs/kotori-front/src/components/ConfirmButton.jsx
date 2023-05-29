import './ConfirmButton.css'

const ConfirmButton = ({ confirm, status, uploadFileCount, completeCount }) => {
  // 仅在blank时隐藏
  let buttonContainerStyle = status == 'blank' ?
    'button-container hide' : 'button-container';

  let uploadText = status == 'uploading' ? <span>上传中...</span> :
    status == 'finish' ? <span>共<span className='upload-file-count'>{completeCount}</span>张图片上传成功, 点击清除</span> :
    <span>上传这<span className="upload-file-count">{uploadFileCount}</span>张图片</span>;

  return (
    <div className={buttonContainerStyle} >
      <button className="confirm-button"
        onClick={() => confirm()}
        disabled={status == 'uploading'}>{uploadText}</button>
    </div>
  )
}

export default ConfirmButton;