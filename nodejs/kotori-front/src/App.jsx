import './App.css'

import {
  Routes,
  Route,
} from "react-router-dom"

import Header from './components/Header'
import Upload from './routes/Upload'
import Footer from './components/Footer'
import NotFound from './routes/NotFound'

function App() {
  const color = 'light';
  return (
    <div className="App">
      <Header color={color}/>
      <Routes>
        <Route index path="/upload" element={<Upload />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
      <Footer color={color}/>
    </div>
  )
}

export default App
