import './LoadMore.css';

function LoadMore({ click }) {
  return (
    <div className='load-more'>
      <button onClick={click}>加载更多</button>
    </div>
  )
}

export default LoadMore