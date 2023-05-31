import ProgressPie from './ProgressPie';
import DeleteButton from './DeleteButton';
import NotSafeMark from './NotSafeMark';
import CopyURLButton from './CopyURLButton';

// 会上传的部分
const WillUpload = ({ d, markNotSafe, showPreview, removeFile, uploadStatus, failedText, openNoteDialog }) => {
  const hidden = uploadStatus == 'uploading' || uploadStatus == 'finish';
  return (
    <li className="upload-list-item">
      <img title={d.fileName} src={d.image} className="preview-image"
        onClick={() => showPreview(d.image)} />
      <span className='right-align'>
        <NotSafeMark id={d.id} markNotSafe={markNotSafe} openNoteDialog={openNoteDialog} />
        <ProgressPie type={d.uploadStatus} progress={d.progress} failedText={failedText} />
        {hidden ?
          <CopyURLButton url={d.uploadURL} /> :
          <DeleteButton click={() => removeFile(d.id)} />
        }
      </span>
    </li>
  )
}

export default WillUpload;