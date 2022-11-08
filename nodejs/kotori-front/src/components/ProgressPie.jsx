import './ProgressPie.css';

const ProgressPie = ({uploaded, total, type}) => {
  let percent = uploaded / total * 360;
  const style = {"--percent": `${percent}deg`}

  let typeText = '待上传';
  if (type == 'uploading') {
    typeText = '上传中...';
  } else if (type == 'uploaded') {
    typeText = '上传完毕';
  }

  let titleText = `已上传:${uploaded}KB / 总共:${total}KB`

  const uploadUpdate = () => {
    console.log('run')
    percent = 0.5;
  }

  return (
    <div className="out-circle" style={style} title={titleText}
      onMouseEnter={() => uploadUpdate()}>
      <div className="in-circle">
        <span>{typeText}</span>
      </div>
    </div>
  )
}

export default ProgressPie;