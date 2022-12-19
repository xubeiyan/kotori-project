import ProgressPie from './ProgressPie';
import DeleteButton from './DeleteButton';

// 会上传的部分
const WillUpload = ({ d, showPreview, removeFile, uploadStatus}) => {
  const hidden = uploadStatus == 'uploading' || uploadStatus == 'finish';
  return (
    <li className="upload-list-item">
      <img title={d.fileName} src={d.image} className="preview-image"
        onClick={() => showPreview(d.image)} />
      <span>文件编号：{d.id}</span>
      <span className='right-align'>
        <ProgressPie type={d.uploadStatus} progress={d.progress} />
        <DeleteButton hidden={hidden} click={() => removeFile(d.id)} />
      </span>
    </li>
  )
}

export default WillUpload;