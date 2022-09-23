import './App.css'

import Header from './components/Header'
import MainPart from './components/MainPart'
import Footer from './components/Footer'


function App() {
  const color = 'light';
  
  return (
    <div className="App">
      <Header color={color} />
      <MainPart color={color} />
      <Footer color={color} />
    </div>
  )
}

export default App
