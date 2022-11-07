import './NotFound.css';

function NotFound() {
  return (
    <div className="not-found">
      <div className='container'>
        <span className='large404'>404</span>
        <span>
          当前
          <span className='pathname'>{window.location.pathname}</span>
          页面不存在
        </span>
      </div>
    </div>
  )
}

export default NotFound;