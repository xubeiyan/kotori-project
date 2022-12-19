import DeleteButton from './DeleteButton';
// 不会上传的部分
const WillNotUpload = ({ d, removeFile }) => {
  return (
    <li className="upload-list-item">
      <span className='not-upload'>文件无法上传</span>
      <span>原因：{d.message}</span>
      <span className='right-align'>
        <DeleteButton hidden={d.uploadStatus == 'uploading'} click={() => removeFile(d.id)} />
      </span>
    </li>
  )
}

export default WillNotUpload;