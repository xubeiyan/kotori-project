import axios from "axios";
import { useEffect } from "react";
import { useState } from "react"

import './ViewList.css';
import LoadMore from "./LoadMore";
import ViewDialog from "./ViewDialog";
import HeartSvg from "../assets/Heart.jsx";

const ViewList = () => {
  // 图片加载状态
  let [status, setStatus] = useState('loaded');
  // 图片列表
  let [list, setList] = useState([]);
  // 分页
  let [page, setPage] = useState({
    p: 1,
    size: 20
  });
  // 详情对话框
  const [dialog, setDialog] = useState({
    open: false,
  });

  // 根据pageSize和pageNum获取下一页
  const getImages = ({ pageNum, pageSize}) => {
    axios.get(`/api/view`, {
      params: {
        p: pageNum,
        size: pageSize,
      },
      headers: {
        'Content-Type': 'application/json'
      }
    }).then(res => {
      if (res.status == 200 && res.data.status) {
        setStatus('loaded');
        setList(res.data.data);
      }
    }).catch(err => console.error(err))
  }

  // 加载更多
  const handleLoadMore = () => {
    getImages({pageNum: 2, pageSize: 20});
  }

  // 打开图片详情对话框
  const showImageDetailDialog = ({ url, likes, time, id}) => {
    setDialog(dialog => ({
      ...dialog,
      open: true,
      url,
      likes,
      upload_time: time,
      uploader_id: id,
    }));
  }

  // 关闭图片详情对话框
  const closeImageDetailDialog = (e) => {
    // 如果点击在对话框本体中则不关闭
    // console.log(e.target.className);
    if (e.target.className != 'dialog-container' && e.target.className != 'close-button') return;

    setDialog(dialog => ({
      ...dialog,
      open: false,
    }))
  }

  useEffect(() => {
    setStatus('loading');
    getImages({pageNum: page.p, pageSize: page.size});
  }, [])


  let listItem = list.map((item, index) =>
    <li className="image-item" key={index}>
      <img className="thumb" src={`images/${item.url}`} />
      <div className="cover" onClick={() => showImageDetailDialog({
        url: item.url,
        likes: item.likes,
        time: item.upload_time,
        id: item.uploader_id,
      })}>
        <div className="likes">
          <div className="heart">
            <HeartSvg />
          </div>
          <span className="likes-num">{item.likes}</span>
        </div>
      </div>
    </li>
  )

  return (status == 'loading' ?
    <span>加载中...</span> :
    <div className="image-list-container">
      <ul className="image-list">
        {listItem}
      </ul>
      <LoadMore click={handleLoadMore} />
      <ViewDialog dialog={dialog} closeDialog={closeImageDetailDialog} />
    </div>
  )
}

export default ViewList