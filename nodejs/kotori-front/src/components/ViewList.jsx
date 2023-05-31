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
  // 加载更多text
  const [loadMoreText, setLoadMoreText] = useState('加载更多');

  // 根据pageSize和pageNum获取下一页
  const getImages = ({ pageNum, pageSize }) => {
    setLoadMoreText('加载中...');
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
        updateImageList(res.data.data);
      }
    }).catch(err => console.error(err))
  }

  // 更新图片列表
  const updateImageList = (toInsertImageList) => {
    // toInsertImageList是空则修改加载更多为’没有更多‘
    if (toInsertImageList.length == 0) {
      setLoadMoreText('没有更多');
      return;
    }

    // list为空则直接更新
    if (list.length == 0) {
      setLoadMoreText('加载更多');
      setList(toInsertImageList);
      return;
    }

    let lastImage = list.slice(-1);
    let otherLastImage = toInsertImageList.slice(-1);
    if (otherLastImage.url !== lastImage.url) {
      setLoadMoreText('加载更多');
      return;
    }

    setList(list => ([
      ...list,
      ...toInsertImageList,
    ]));
    setLoadMoreText('加载更多');
  }

  // 加载更多
  const handleLoadMore = () => {
    getImages({ pageNum: page.p + 1, pageSize: 20 });
  }

  // 打开图片详情对话框
  const showImageDetailDialog = ({ url, likes, time, id }) => {
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
    getImages({ pageNum: page.p, pageSize: page.size });
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
      <LoadMore click={handleLoadMore} text={loadMoreText}/>
      <ViewDialog dialog={dialog} closeDialog={closeImageDetailDialog} />
    </div>
  )
}

export default ViewList