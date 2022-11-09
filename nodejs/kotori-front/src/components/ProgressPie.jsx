import { useState } from 'react';
import { useEffect } from 'react';
import './ProgressPie.css';

const ProgressPie = ({progress = 0, type}) => {
  let [uploadStyle, setUploadStyle] = useState({"--percent": `0deg`});

  let [typeText, setTypeText] = useState('待上传');

  useEffect(()=> {
    let percent = progress * 360;
    setUploadStyle({"--percent": `${percent}deg`});
    // console.log('uploaded:', uploaded)
  }, [progress]);

  useEffect(() => {
    if (type == 'uploading') {
      setTypeText('正在上传');
    } else if (type == 'uploaded') {
      setTypeText('上传完成');
    }
  })

  return (
    <div className="out-circle" style={uploadStyle} >
      <div className="in-circle">
        <span>{typeText}</span>
      </div>
    </div>
  )
}

export default ProgressPie;