import { useState } from 'react';
import './App.css'

import Header from './components/Header'
import MainPart from './components/MainPart'
import Footer from './components/Footer'

import ColorContext from './context/colorContext.js';

function App() {
  const colorTexts = ['🌞', '🌙']
  const [toggleText, setToggleText] = useState(() => {
    const savedColor = localStorage.getItem('colorScheme');
    return savedColor || colorTexts[0];
  });
  const value = {toggleText, setToggleText};
  
  return (
    <ColorContext.Provider value={value}>
      <div className="App">
        <Header />
        <MainPart />
        <Footer />
      </div>
    </ColorContext.Provider>
  )
}

export default App
