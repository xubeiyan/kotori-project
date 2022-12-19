import { useState } from "react";
import { uploadURI } from '../uploadConfig';

const useUploadForm = ({ formData }) => {
  // const [isSuccess, setIsSuccess] = useState(false);
  const [progress, setProgress] = useState(0);

  const uploadForm = (formData) => {
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
      // completeCount.current += 1;
    });
  };

  return { uploadForm, progress };
};

export default useUploadForm;