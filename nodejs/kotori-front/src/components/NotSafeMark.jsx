import './NotSafeMark.css';

const NotSafeMark = ({ id, markNotSafe }) => {
  return <span className='nsfw-mark' title='勾选上这个选项就只能通过URL访问到此图片'>
    <label style={{ paddingRight: `.25em` }}>NSFW</label>
    <input type="checkbox" onChange={() => markNotSafe(id)} />
  </span>
}


export default NotSafeMark;