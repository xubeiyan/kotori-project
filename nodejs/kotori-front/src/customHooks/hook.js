import { useState } from "react";

const useUploadForm = (url) => {
  const [isSuccess, setIsSuccess] = useState(false);
  const [progress, setProgress] = useState(0);

  const uploadForm = (formData) => {
    let xhr = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', (e) => {
      setProgress(e.loaded / e.total * 100);
    });
    
    xhr.open('post', url);
    xhr.send(formData);

    xhr.addEventListener('readystatechange', (e) => {
      console.log(e);
      setIsSuccess(true);
    })
  };

  return { uploadForm, isSuccess, progress };
};

export default useUploadForm;