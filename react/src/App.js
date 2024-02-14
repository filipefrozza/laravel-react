import logo from './logo.svg';
import React, { useEffect } from 'react';
import './App.css';
import Rastreio from './rastreio/Rastreio';

function App() {
  useEffect(() => {
    document.title = "Rastreio por CPF / CNPJ Brudam";
  }, []);
  
  return (
    <div className="App">
      <header className="App-header">
        <p className='mt-5'>
          Teste de rastreio por CPF / CNPJ Brudam com Laravel e React
        </p>
        <Rastreio />
      </header>
    </div>
  );
}

export default App;
