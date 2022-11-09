import './DeleteButton.css';

function DeleteButton({hidden, click}) {
  const className = hidden ? 'delete-button hidden' : 'delete-button'; 
  return (
    <button className={className} onClick={click} disabled={hidden}>
      &#215;
    </button>
  )
}

export default DeleteButton;