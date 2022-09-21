function MenuItem({text, url}) {
  return (
    <span className="menu-item">
      <a href={url} >{ text }</a>
    </span>
  )
}

export default MenuItem;