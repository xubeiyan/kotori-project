import { useState } from 'react';
import Preview from './Preview';
import ConfirmButton from './ConfirmButton';
import UploadList from './UploadList';
import './UploadPart.css';
import axios from 'axios';
import { uploadURI } from '../uploadConfig';

// 允许的文件类型
const acceptedFileType = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
// 最大允许上传文件数
const IMAGE_MAX_NUM = 10;
// 最大允许的文件大小
const PER_IMAGE_MAX_SIZE = 2 * 1024 * 1024;

const UploadPart = () => {
  // 当前状态，默认为 blank, add, uploading, finish
  let [status, setStatus] = useState('blank');

  // 待上传的文件
  let [resultData, setResultData] = useState([]);

  // 查看预览图状态
  let [preview, setPreview] = useState({
    status: 'hide',
    src: '',
  });

  // 上传成功数
  let [successCount, setSuccessCount] = useState(0);

  // 点击上传按钮
  const openUploadDialog = () => {
    const inputForm = document.querySelector('input.file');
    inputForm.click();
  }

  // 拖放文件（支持多个）
  const dropFile = (e) => {
    e.preventDefault();
    let fileList = e.dataTransfer.files;
    if (fileList.length == 0) return;
    validateFile(fileList);
  }

  // 选择文件（支持多个）
  const fileSelect = (e) => {
    let fileList = e.target.files;
    if (fileList.length == 0) return;
    validateFile(fileList);
  }

  // 阻止浏览器默认操作
  const preventDefault = (e) => {
    e.preventDefault();
    e.stopPropagation();
  }

  // 验证文件
  const validateFile = (fileList) => {
    let uploadList = [];
    for (const value of fileList) {
      let pushObj = {
        fileType: value.type,
        error: false,
      };
      // 验证是否是允许的类型
      if (!acceptedFileType.includes(value.type)) {
        pushObj.error = true;
        pushObj.message = `不允许上传'${value.type == '' ? '未知' : value.type}'类型的文件`;
      } else if (value.size > PER_IMAGE_MAX_SIZE) {
        pushObj.error = true;
        pushObj.message = `文件大小为${Math.floor(value.size / 1024)}KB，超过了大小限制${Math.floor(PER_IMAGE_MAX_SIZE / 1024)}KB`;
      } else {
        pushObj.image = window.URL.createObjectURL(value);
        pushObj.imageObj = value;
        pushObj.fileName = value.name;
        pushObj.size = Math.floor(value.size / 1024);
        // pushObj.uploadedSize = 0;
      }
      uploadList.push(pushObj);
    }
    showFileDetails(uploadList);
  }
  // 显示待上传文件
  const showFileDetails = (uploadList) => {
    if (resultData.length == 0) {
      setStatus('add');
    }

    if (resultData.length + uploadList.length > IMAGE_MAX_NUM) {
      console.log('超过了最大允许数量');
      return;
    }

    let d = [...resultData, ...uploadList];
    // 为其增加id
    d.forEach((one, index) => { one.id = index })
    setResultData(d);
  }

  // 移除文件
  const removeFile = (index) => {
    // console.log(`将要删除id为${index}的文件`);
    let filtedData = resultData.filter(value => value.id !== index)

    setResultData(filtedData);

    if (filtedData.length == 0) {
      setStatus('blank');
    }
  }

  // 修改单个文件的上传和状态
  const modifySingleFileStatus = ({index, progress}) => {
    let certain = resultData[index];
    certain.progress = progress;
    if (progress > 0) certain.uploadStatus = 'uploading';
    if (progress >= 1) certain.uploadStatus = 'uploaded';

    let prev = resultData.slice(0, index);
    let next = resultData.slice(index + 1);

    setResultData([...prev, certain, ...next])
  }

  // 根据返回结果确定上传成功或失败
  const modifySingleFileUpload = ({index, success}) => {
    let certain = resultData[index];
    // certain.progress = progress;
    if (success) {
      certain.uploadStatus = 'uploaded';
      setSuccessCount(successCount => successCount + 1);
    } else {
      certain.uploadStatus = 'failed';
    }

    let prev = resultData.slice(0, index);
    let next = resultData.slice(index + 1);

    setResultData([...prev, certain, ...next])
  }

  // 进行上传
  const confirmUpload = () => {
    // 上传成功后再次点击重置所有
    if (status == 'finish') {
      setStatus('blank');
      setResultData([]);
      setSuccessCount(0);
      return;
    }

    let noErrorData = resultData.filter(value => value.error == false);
    // console.log(noErrorData)
    let toUploadData = noErrorData.map(({imageObj, size}) => ({
      imageObj, size,
      uploadedSize: 0
    }));
    
    let toUploadLength = toUploadData.length;
    // 没有上传文件则不上传
    if (toUploadLength == 0) return;
    
    setStatus('uploading');

    resultData.forEach((data, index) => {
      if (data.error) return;

      axios.post(uploadURI, {
        image: data.imageObj
      },{
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: (e) => {
          const progress = e.loaded / e.total;
          modifySingleFileStatus({index, progress});
          
        },
      }).then(res => {
        if (res.status == 200)  {
          modifySingleFileUpload({index, success: res.data.status});
          
          setStatus('finish');
        }
      });
    });
    
    // console.log(toUploadData);
  }

  // 待上传文件个数
  const toUploadCount = () => {
    let filtedData = resultData.filter(value => value.error == false);
    return filtedData.length;
  } 

  // 切换nsfw标记
  const markNotSafe = () => {
    
  }


  return (
    <div className='upload-part'>
      <div className={status == 'blank' ? "upload-button" : "upload-button thin"}
        onClick={openUploadDialog} onDrop={dropFile}
        onDragLeave={preventDefault}
        onDragEnter={preventDefault}
        onDragOver={preventDefault}
      >
        点击这里选择文件或者是把文件拖放到这里
        <input type="file" className='file hide' multiple="multiple" onChange={fileSelect} />
      </div>
      <UploadList 
        data={resultData} removeFile={removeFile} 
        setPreview={setPreview} uploadStatus={status} 
        markNotSafe={markNotSafe}
        />
      <Preview status={preview.status} setPreview={setPreview} imgSrc={preview.src} />
      <ConfirmButton confirm={confirmUpload} status={status} uploadFileCount={toUploadCount()} completeCount={successCount}/>
    </div>
  )
}

export default UploadPart;