function Pagination({page, setPage}) {
  // 可选每页数
  const availiablePerPage = [10, 25];
  // 总页数
  const totalPage = Math.ceil(page.total / page.perPage);
  // 改变分页值
  const handleChange = (field, value) => {
    setPage(page => ({
      ...page,
      [field]: value,
    }))
  }
  return (
    <div>
      <span>每页
        <select value={page.perPage} 
        onChange={(e) => handleChange('perPage', e.target.value)}>{availiablePerPage.map(one => (
          <option key={one} value={one}>{one}</option>
        ))}</select>
        项</span>
      <span>共{page.total}项 {page.current} / {totalPage}</span>
      <span></span>
    </div>
  )
}

export default Pagination;