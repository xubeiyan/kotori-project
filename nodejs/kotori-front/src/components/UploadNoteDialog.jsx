import './UploadNoteDialog.css';
import CloseButton from './CloseButton';

function UploadNoteDialog({ dialog, closeDialog }) {
  return (
    <>
      {dialog.open ?
        <div className='dialog-container' onClick={closeDialog}>
          <div className='dialog-small'>
            <span className='title'>NSFW标签说明</span>
            <p>NSFW(Not Safe For Work 或者 Not Suitable For Work)标签意味这这张图是不适合在工作场合或者公开展示，
              通常这张图片具有某些不适合上班时段观看、可能会冒犯上司或同事的内容，多指裸露、暴力、色情或冒犯等不适宜公众场合的内容。</p>
            <p>当图片被指定为NSFW后，将无法在查看页面中展示。也就是说，除了上传时能够获得它的的链接，而链接是通过 UUID 生成，很难记忆，
              也就意味着其他时候都是隐私的。</p>
            <CloseButton click={closeDialog}/>
          </div>
        </div>
        : null
      }
    </>
  )
}

export default UploadNoteDialog