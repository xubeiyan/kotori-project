import ProgressPie from './ProgressPie';
import DeleteButton from './DeleteButton';

import { useState } from 'react';
import { useEffect } from 'react';
import './UploadList.css';

// 上传路径
import { uploadURI } from '../uploadConfig';

const UploadList = ({ data, removeFile, setPreview, uploadStatus}) => {
  const showPreview = (src) => {
    setPreview(preview => {
      return { ...preview, src, status: 'show'};
    })
  }

  const NotUpload = ({d, index}) => {
    return (
      <li className="upload-list-item">
        <span className='not-upload'>文件无法上传</span>
        <span>原因：{d.message}</span>
        <span className='right-align'>
          <DeleteButton click={() => removeFile(d.id)} />
        </span>
      </li>
    )
  }
  
  const ToUpload = ({d, index}) => {
    let xhr = new XMLHttpRequest();
    let form = new FormData();
    form.append('image', d.imageObj);
  
    let [progress, setProgress] = useState(0);
    let [singleUpload, setSingleUpload] = useState('toupload');
  
    xhr.upload.addEventListener('progress', (e) => {
      // console.log(e.loaded, e.total);
      setProgress(e.loaded / e.total);
    });

    xhr.addEventListener('readystatechange', (e) => {
      // console.log(e.target)
      if (e.target.readyState == 4) {
        setSingleUpload('uploaded');
      }
    })
  
    useEffect(() => {
      if (uploadStatus == 'uploading') {
        xhr.open('post', uploadURI);
        xhr.send(form);
        setSingleUpload('uploading')
      }
    }, [uploadStatus]);
    
    return (
      <li className="upload-list-item">
        <img title={d.fileName} src={d.image} className="preview-image"
          onClick={() => showPreview(d.image)} />
        <span>文件编号：{d.id}</span>
        <span className='right-align'>
          <ProgressPie type={singleUpload} progress={progress}/>
          <DeleteButton hidden={singleUpload == 'uploading'} click={() => removeFile(d.id)} />
        </span>
      </li>
    )
  }

  if (data.length == 0) return ('');

  const listItem = data.map((d, index) => {
    return d.error ? <NotUpload key={index} d={d} index={index}/> : <ToUpload key={index} d={d} index={index}/>
  });

  return (
    <ul className='upload-list'>
      {listItem}
    </ul>
  )
}

export default UploadList;