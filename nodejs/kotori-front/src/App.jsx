import './App.css'

import {
  Routes,
  Route,
} from "react-router-dom"

import Header from './components/Header'
import Upload from './routes/Upload'
import Footer from './components/Footer'

function App() {
  return (
    <div className="App">
      <Header />
      <Routes>
        <Route index element={<Upload />} />
      </Routes>
      <Footer />
    </div>
  )
}

export default App
