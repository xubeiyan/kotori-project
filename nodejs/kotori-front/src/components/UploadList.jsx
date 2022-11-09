import ProgressPie from './ProgressPie';
import DeleteButton from './DeleteButton';

import './UploadList.css';

const UploadList = ({ data, removeFile, setPreview }) => {
  const showPreview = (src) => {
    setPreview((preview) => {
      return { ...preview, src };
    })
  }

  if (data.length == 0) return ('');

  const listItem = data.map((d, index) => {
    if (d.error) {
      return (
        <li key={index} className="upload-list-item">
          <span className='not-upload'>文件无法上传</span>
          <span>原因：{d.message}</span>
          <span className='right-align'>
            <DeleteButton click={() => removeFile(d.id)} />
          </span>
        </li>
      )
    } else {
      return (
        <li key={index} className="upload-list-item">
          <img title={d.fileName} src={d.image} className="preview-image"
            onClick={() => showPreview(d.image)} />
          <span>文件编号：{d.id}</span>
          <span className='right-align'>
            <ProgressPie />
            <DeleteButton click={() => removeFile(d.id)} />
          </span>
        </li>
      )
    }
  });

  return (
    <ul className='upload-list'>
      {listItem}
    </ul>
  )
}

export default UploadList;