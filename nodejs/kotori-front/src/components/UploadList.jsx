import ProgressPie from './ProgressPie';
import DeleteButton from './DeleteButton';
import axios from 'axios';

import { useState } from 'react';
import { useEffect } from 'react';
import './UploadList.css';

// 上传路径
import { uploadURI } from '../uploadConfig';
import { useRef } from 'react';

const UploadList = ({ data, removeFile, setPreview, confirmStatus }) => {
  let completeCount = useRef(0);

  // 显示预览图
  const showPreview = (src) => {
    setPreview(preview => {
      return { ...preview, src, status: 'show' };
    })
  }

  const NotUpload = ({ d, upload }) => {
    return (
      <li className="upload-list-item">
        <span className='not-upload'>文件无法上传</span>
        <span>原因：{d.message}</span>
        <span className='right-align'>
          <DeleteButton hidden={upload == 'uploading'} click={() => removeFile(d.id)} />
        </span>
      </li>
    )
  }

  const ToUpload = ({ d, upload }) => {
    let [progress, setProgress] = useState(0);
    let [singleUpload, setSingleUpload] = useState('toupload');

    useEffect(() => {
      if (upload == 'uploading') {
        setSingleUpload('uploading');
        axios.post(uploadURI, {
          image: d.imageObj,
        }, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
          onUploadProgress: (e) => {
            const progress = e.loaded / e.total;
            setProgress(progress);
          },
        }).then(res => {
          if (res.status == 200) {
            setSingleUpload('uploaded');
          } else {
            setSingleUpload('failed');
          }
          completeCount.current += 1;
        });
        // let xhr = new XMLHttpRequest();
        // let form = new FormData();
        // form.append('image', d.imageObj);

        // xhr.upload.addEventListener('progress', (e) => {
        //   // console.log(e.loaded, e.total);
        //   setProgress(e.loaded / e.total);
        // });

        // xhr.addEventListener('load', (e) => {
        //   // console.log(e.target)
        //   setSingleUpload('uploaded');
        //   // setCompleteCount(completeCount + 1);
        // });

        // xhr.open('post', uploadURI, true);
        // xhr.send(form);
        // 
      }
      // console.log(confirmStatus);
    }, [upload]);

    return (
      <li className="upload-list-item">
        <img title={d.fileName} src={d.image} className="preview-image"
          onClick={() => showPreview(d.image)} />
        <span>文件编号：{d.id}</span>
        <span className='right-align'>
          <ProgressPie type={singleUpload} progress={progress} />
          <DeleteButton hidden={singleUpload != 'toupload'} click={() => removeFile(d.id)} />
        </span>
      </li>
    )
  }

  if (data.length == 0) return ('');

  const listItem = data.map((d, index) => {
    return d.error ? <NotUpload key={index} d={d} upload={confirmStatus}/> : <ToUpload key={index} d={d} upload={confirmStatus} />
  });

  return (
    <ul className='upload-list'>
      {listItem}
    </ul>
  )
}

export default UploadList;