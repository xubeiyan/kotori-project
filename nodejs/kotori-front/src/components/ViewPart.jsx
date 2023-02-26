import axios from "axios";
import { useState, useEffect } from "react";

import ViewFlexBox from "./ViewGrid";
import Pagination from "./Pagination";

function ViewPart() {
  // 分页
  const [page, setPage] = useState({
    current: 1,
    perPage: 20,
    total: 0,
  });

  // 文件列表
  const [viewList, setViewList] = useState([])

  // 获取文件
  const getViewList = () => {
    axios.get('/api/view', {
      params: {
        current: page.current,
        perPage: page.perPage
      }
    }).then(res => {
      console.log(res);
    })
  }

  useEffect(() => {
    // getViewList();
  }, []);

  return (
    <div>
      <ViewFlexBox viewList={viewList}/>
      <Pagination page={page} setPage={setPage}/>
    </div>
  )
}

export default ViewPart;