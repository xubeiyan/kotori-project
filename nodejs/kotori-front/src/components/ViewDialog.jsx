import CloseButton from './CloseButton';
import './ViewDialog.css';

function ViewDialog({ dialog, closeDialog }) {
  // 下载文件
  const downloadImage = () => {
    const link = document.createElement('a');
    const extname = dialog.url.split('.')[1];

    fetch(`images/${dialog.url}`)
      .then(res => res.blob())
      .then(blob => URL.createObjectURL(blob))
      .then(href => {
        link.href = href;
        link.download = `myimage.${extname}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      })
  }

  // 在新标签页打开图片
  const openInNewTab = () => {
    window.open(`images/${dialog.url}`, '_blank');
  }

  return (
    <>
      {dialog.open ?
        <div className='dialog-container' onClick={closeDialog}>
          <div className='dialog'>
            <img src={`images/${dialog.url}`} className='image' onClick={openInNewTab} title='单击在新页面打开图片' />
            <div className='status-bar'>
              <span>有 {dialog.likes} 人表示了喜欢</span>
              <span>由 ID 为 {dialog.uploader_id} 的用户上传于 {dialog.upload_time}</span>
              <button className='download-button' onClick={downloadImage}>下载图片</button>
            </div>
            <CloseButton click={closeDialog}/>
          </div>
        </div>
        : null}
    </>
  )
}

export default ViewDialog