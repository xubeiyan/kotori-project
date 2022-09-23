import { useContext } from 'react';
import ColorContext from '../context/colorContext.js';

import './MainPart.css';

import {
  Routes,
  Route,
} from "react-router-dom"

import NotFound from '../routes/NotFound'
import Upload from '../routes/Upload'

function MainPart() {
  const { toggleText } = useContext(ColorContext);
  let colorScheme = toggleText == '🌞' ? 'color-light' : 'color-dark';
  let mainPartColor = `main-part ${colorScheme}`;

  return (
    <div className={mainPartColor} >
      <Routes>
        <Route index path="/" element={<Upload />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </div>
  )
}

export default MainPart;