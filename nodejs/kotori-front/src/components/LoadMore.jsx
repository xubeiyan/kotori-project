import './LoadMore.css';

function LoadMore({ click, text }) {
  return (
    <div className='load-more'>
      <button onClick={click} disabled={text !== '加载更多'}>{text}</button>
    </div>
  )
}

export default LoadMore