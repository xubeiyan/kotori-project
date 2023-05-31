import './NotSafeMark.css';

const NotSafeMark = ({ id, markNotSafe, openNoteDialog }) => {
  return <span className='nsfw-mark'>
    <label style={{ paddingRight: `.25em` }}>NSFW</label>
    <input type="checkbox" onChange={() => markNotSafe(id)} />
    <button className='nsfw-mark-help' onClick={openNoteDialog}>?</button>
  </span>
}


export default NotSafeMark;