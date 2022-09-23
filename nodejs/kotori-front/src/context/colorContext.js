import { createContext } from "react";

const ColorContext = createContext({
  toggleText: '🌙',
  setToggleText: () => {}
}); 

export default ColorContext;