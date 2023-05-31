import './CloseButton.css';

function CloseButton({ click }) {
  return (
    <div className='close-button' onClick={click}>&#215;</div>
  )
}

export default CloseButton