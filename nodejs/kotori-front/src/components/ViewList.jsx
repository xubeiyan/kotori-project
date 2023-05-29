import axios from "axios";
import { useEffect } from "react";
import { useState } from "react"

import './ViewList.css';
import LoadMore from "./LoadMore";

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

  // 根据pageSize和pageNum获取下一页
  const getImages = () => {
    axios.get(`/api/view`, {
      params: {
        p: page.p,
        size: page.size
      },
      headers: {
        'Content-Type': 'application/json'
      }
    }).then(res => {
      if (res.status == 200 && res.data.status) {
        setStatus('loaded');
        setList(list => ([...res.data.data]));
        setPage(page => ({
          ...page,
          p: page.p + 1,
        }))
      }
    }).catch(err => console.error(err))
  }

  // 加载更多
  const handleLoadMore = () => {

  }

  useEffect(() => {
    setStatus('loading');
    getImages();
  }, [])


  let listItem = list.map((item, index) =>
    <li className="image-item" key={index}>
      <img className="thumb" src={`images/${item.url}`} />
    </li>
  )

  return (status == 'loading' ?
    <span>加载中...</span> :
    <div className="image-list-container">
      <ul className="image-list">
        {listItem}
      </ul>
      <LoadMore click={handleLoadMore} />
    </div>
  )
}

export default ViewList