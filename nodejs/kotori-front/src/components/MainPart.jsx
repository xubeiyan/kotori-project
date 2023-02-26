import { useContext } from 'react';
import ColorContext from '../context/colorContext.js';

import './MainPart.css';

import {
  Routes,
  Route,
} from "react-router-dom"

import NotFound from '../routes/NotFound';
import Upload from '../routes/Upload';
import Main from '../routes/Main';
import View from '../routes/View.jsx';

function MainPart() {
  const { toggleText } = useContext(ColorContext);
  let colorScheme = toggleText == '🌞' ? 'color-light' : 'color-dark';
  let mainPartColor = `main-part ${colorScheme}`;

  return (
    <div className={mainPartColor} >
      <Routes>
        <Route path='/' element={<Main />} />
        <Route index path="/upload" element={<Upload />} />
        <Route path='/view' element={<View />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </div>
  )
}

export default MainPart;