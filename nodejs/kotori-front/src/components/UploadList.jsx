import WillNotUpload from './WillNotUpload';
import WillUpload from './WillUpload';

import './UploadList.css';

const UploadList = ({ data, removeFile, setPreview, uploadStatus }) => {

  // 显示预览图
  const showPreview = (src) => {
    setPreview(preview => {
      return { ...preview, src, status: 'show' };
    })
  }

  if (data.length == 0) return ('');

  const listItem = data.map((d, index) => {
    return d.error ? 
      <WillNotUpload key={index} d={d} removeFile={removeFile}/> : 
      <WillUpload key={index} d={d} showPreview={showPreview} uploadStatus={uploadStatus} removeFile={removeFile} />
  });

  return (
    <ul className='upload-list'>
      {listItem}
    </ul>
  )
}

export default UploadList;