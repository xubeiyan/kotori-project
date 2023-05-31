import './DeleteButton.css';

function DeleteButton({ click }) {
  return (
    <button className='delete-button' onClick={click} >
      &#215;
    </button>
  )
}

export default DeleteButton;