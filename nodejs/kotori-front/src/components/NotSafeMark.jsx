import './NotSafeMark.css';

const NotSafeMark = ({ id, markNotSafe }) => {
  return <span className='nsfw-mark'>
    <label>nsfw</label>
    <input type="checkbox" onChange={() => markNotSafe(id)} />
  </span>
}


export default NotSafeMark;