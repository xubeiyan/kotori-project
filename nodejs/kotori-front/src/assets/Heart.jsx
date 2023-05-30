import React from 'react'

function heart() {
  return (
    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
      <path stroke="red" strokeWidth={5} fill="rgba(0,0,0,0)" d='M 10,30A 20,20 90,0,1 50,30A 20,20 0,0,1 90,30Q 90,60 50,90Q 10,60 10,30 z' />
    </svg>
  )
}

export default heart