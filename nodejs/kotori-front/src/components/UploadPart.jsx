import { useState } from 'react';
import Preview from './Preview';
import ConfirmButton from './ConfirmButton';
import UploadList from './UploadList';
import './UploadPart.css';

// 上传路径
import { uploadURI } from '../uploadConfig';

import useUploadForm from '../customHooks/hook';


// 允许的文件类型
const acceptedFileType = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
// 最大允许上传文件数
const IMAGE_MAX_NUM = 10;
// 最大允许的文件大小
const PER_IMAGE_MAX_SIZE = 2 * 1024 * 1024;

function UploadPart() {
  // 当前状态，默认为 blank, add
  let [status, setStatus] = useState('blank');

  // 待上传的文件
  let [resultData, setResultData] = useState([]);

  // 查看预览图状态
  let [preview, setPreview] = useState({
    status: 'hide',
    src: '',
  });

  // 确认上传按钮状态
  let [confirmStatus, setConfirmStatus] = useState('hide');

  // 点击上传按钮
  const openUploadDialog = () => {
    const inputForm = document.querySelector('input.file');
    inputForm.click();
  }

  // 拖放文件（支持多个）
  const dropFile = (e) => {
    e.preventDefault();
    let fileList = e.dataTransfer.files;
    validateFile(fileList);
  }

  // 选择文件（支持多个）
  const fileSelect = (e) => {
    let fileList = e.target.files;
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
        pushObj.uploadedSize = 0;
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
    setConfirmStatus('show');
  }

  // 移除文件
  const removeFile = (index) => {
    // console.log(`将要删除id为${index}的文件`);
    let filtedData = resultData.filter(value => value.id !== index)

    setResultData(filtedData);

    if (filtedData.length == 0) {
      setStatus('blank');
      setConfirmStatus('hide');
    }
  }



  

  // 确认上传
  const confirmUpload = () => {
    setConfirmStatus('uploading');
    for (const index in resultData) {
      let one = resultData[index];
      if (one.error) {
        return;
      }



      xhr.open('post', uploadURI);
      xhr.send(form);
    }
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
      <UploadList data={resultData} removeFile={removeFile} setPreview={setPreview}/>
      <Preview status={preview.status} setStatus={setPreview} imgSrc={preview.src} />
      <ConfirmButton confirm={confirmUpload} status={confirmStatus} uploadFileCount={resultData.length} />
    </div>
  )
}

export default UploadPart;