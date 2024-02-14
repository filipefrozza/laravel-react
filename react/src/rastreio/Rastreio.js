import React, { useEffect, useState } from 'react';
import axios from 'axios';

const Rastreio = () => {
  const [cpfCnpj, setCpfCnpj] = useState('');
  const [resultados, setResultados] = useState([]);
  const [section, setSection] = useState('form');
  

  useEffect(() => {
    
  }, []);
  
  const handleInputChange = (event) => {
    setCpfCnpj(event.target.value);
  };

  const buscarRastreio = async () => {
    setSection('loading');
    try {
      const response = await axios.get('http://localhost:8000/api/clientes/' + cpfCnpj);
      setResultados(response.data);
      setSection('resultados');
      console.log(resultados);
    } catch (error) {
      setSection('resultados');
      setResultados(error.response.data);
      console.error('Erro ao buscar rastreio:', error);
    }
  };

  const formRastreio = () => {
    return <div>
      <input
        className='form-control mb-2'
        type="text"
        value={cpfCnpj}
        onChange={handleInputChange}
        placeholder="Digite seu CPF/CNPJ"
      />
      <button className='btn btn-primary' onClick={buscarRastreio}>Buscar</button>
    </div>
  }
  
  const loadingRastreio = () => {
    return <div>
      <p>Buscando...</p>
    </div>
  }
  
  const dadosRastreio = () => {
    return <div>
      <div>
        <p>Cliente {resultados.data.cliente.nome}</p>
      </div>
      <div className='entregas mb-5'>
        {resultados.data.entregas.map((entrega) => {
          return <div className='card m-auto entrega mb-3'>
            <h4 className='mt-3'>Entrega #{entrega.api_id}</h4>
            <hr />
            <p>Remetente: {entrega.remetente}</p>
            <p>Destinatario: {resultados.data.cliente.nome}</p>
            <p>Volumes: {entrega.volumes}</p>
            <p>Transportadora: {entrega.transportadoras.fantasia}</p>
            <p>Rastreios:</p>
            {
              entrega.rastreamentos.map((rastreamento) => {
                return <div className='card m-auto p-5 mb-3 rastreamento'>
                  <p>{rastreamento.date}</p>
                  <p>{rastreamento.message}</p>
                </div>
              })
            }
          </div>
        })}
      </div>
    </div>
  }
  
  const erroRastreio = () => {
    
  }
  
  const resultadosRastreio = () => {
    return <div>
      <p>Resultado da busca: </p>
      <button className='btn btn-danger mb-3' onClick={() => setSection('form')}>Voltar</button><br />
      <small>{resultados.message}</small>
      {
        resultados.code == 200 ?
        dadosRastreio():
        erroRastreio()
      }
    </div>
  }
  
  return (
    <div>
      {
        section == 'form' ? formRastreio() : section == 'loading' ? loadingRastreio() : resultadosRastreio()
      }
    </div>
  );
};

export default Rastreio;