import ProgressPie from './ProgressPie';
import DeleteButton from './DeleteButton';
import NotSafeMark from './NotSafeMark';

// 会上传的部分
const WillUpload = ({ d, markNotSafe, showPreview, removeFile, uploadStatus}) => {
  const hidden = uploadStatus == 'uploading' || uploadStatus == 'finish';
  return (
    <li className="upload-list-item">
      <img title={d.fileName} src={d.image} className="preview-image"
        onClick={() => showPreview(d.image)} />
      <span className='right-align'>
        <NotSafeMark id={d.id} markNotSafe={markNotSafe}/>
        <ProgressPie type={d.uploadStatus} progress={d.progress} />
        <DeleteButton hidden={hidden} click={() => removeFile(d.id)} />
      </span>
    </li>
  )
}

export default WillUpload;