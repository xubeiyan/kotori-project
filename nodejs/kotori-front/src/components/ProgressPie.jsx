import { useState } from 'react';
import { useEffect } from 'react';
import './ProgressPie.css';

const ProgressPie = ({ progress = 0, type, failedText }) => {
  let [uploadStyle, setUploadStyle] = useState({ "--percent": `0deg` });

  useEffect(() => {
    let percent = progress * 360;
    setUploadStyle({ "--percent": `${percent}deg` });
    // console.log('uploaded:', uploaded)
  }, [progress]);

  let typeText = type == 'failed' ? <span className='warning'>上传失败</span> :
    type == 'uploaded' ? '上传完成' :
      type == 'uploading' ? '上传中' : '待上传';

  return (
    <div className="out-circle" style={uploadStyle} >
      <div className="in-circle" title={type == 'failed' ? failedText : null}>
        <span>{typeText}</span>
      </div>
    </div>
  )
}

export default ProgressPie;