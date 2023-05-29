import axios from "axios";
import { useEffect } from "react";
import { useState } from "react"

import { backendURI } from "../uploadConfig";
import './ViewList.css';

const ViewList = () => {
  let [status, setStatus] = useState('loaded');
  let [list, setList] = useState([]);
  let [page, setPage] = useState({
    p: 1,
    size: 20
  });

  useEffect(()=> {
    setStatus('loading');
    axios.get(`${backendURI}/view`, {
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
        setList(res.data.data);
      }
    }).catch(err => console.error(err))
  }, [page])
  

  let listItem = list.map((value, index) => 
    <li key={index}><img className="thumb" src={value.url} /></li>
  )

  return (status == 'loading' ? 
    <span>加载中...</span> :
    <ul>
      {listItem}
    </ul>
    )
}

export default ViewList