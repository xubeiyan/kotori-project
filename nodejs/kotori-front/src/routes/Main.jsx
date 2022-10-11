import './Main.css';
import { Link } from 'react-router-dom'

function Main() {
  return (
    <div className="flex-center main-area">
      <span className='title'> 欢迎使用Kotori Project</span>
      <span className='content'>
        它是一个网络相册(Image hosting service)，你可以<Link to={"/upload"}>上传图片</Link>（支持JPG, PNG, GIF, 以及WebP）供其他人浏览。
      </span>
      <span className='content'>每张图的大小限制为2MiB。</span>
    </div>
  );
}

export default Main