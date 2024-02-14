<?php

namespace App\Http\Controllers;

// Para facilitar o datetime parse
use Carbon\Carbon;

use Illuminate\Http\Request;
// Para acessar api externa
use Illuminate\Support\Facades\Http;

// Model principal
use App\Models\Clientes;

// Models para fazer o processo de sync com a api
use App\Models\Rastreamentos;
use App\Models\Entregas;
use App\Models\Transportadoras;

class ClienteController extends Controller
{
    protected $bydoc_description = 'Api de Listagem de Entregas por CPF/CNPJ';
    public function index()
    {
        $clientes = Clientes::all();
        return response()->json($clientes);
    }
    
    public function store(Request $request)
    {
        $cliente = new Clientes;
        $cliente->cpfcnpj = $request->cpfcnpj;
        $cliente->nome = $request->nome;
        $cliente->endereco = $request->endereco;
        $cliente->estado = $request->estado;
        $cliente->cep = $request->cep;
        $cliente->pais = $request->pais;
        $cliente->geolocalizao = $request->geolocalizao;
        $cliente->save();
        return response()->json([
            "message" => "Cliente cadastrada!"
        ], 201);
    }
    
    public function show($id)
    {
        $cliente = Clientes::find($id);
        if(!empty($cliente))
        {
            return response()->json($cliente);
        }
        else
        {
            return response()->json([
                "message" => "Cliente não encontrada"
            ], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        if(Clientes::where('id', $id)->exists())
        {
            $cliente = Clientes::find($id);
            $cliente->cpfcnpj = is_null($request->cpfcnpj) ? $cliente->cpfcnpj : $request->cpfcnpj;
            $cliente->nome = is_null($request->nome) ? $cliente->nome : $request->nome;
            $cliente->endereco = is_null($request->endereco) ? $cliente->endereco : $request->endereco;
            $cliente->estado = is_null($request->estado) ? $cliente->estado : $request->estado;
            $cliente->cep = is_null($request->cep) ? $cliente->cep : $request->cep;
            $cliente->pais = is_null($request->pais) ? $cliente->pais : $request->pais;
            $cliente->geolocalizao = is_null($request->geolocalizao) ? $cliente->geolocalizao : $request->geolocalizao;
            $cliente->save();
            return response()->json([
                "message" => "Cliente atualizada"
            ], 200);
        } 
        else 
        {
            return response()->json([
                "message" => "Cliente não encontrada"
            ], 404);
        }
    }
    
    public function destroy($id)
    {
        if(Clientes::where('id', $id)->exists())
        {
            $cliente = Clientes::find($id);
            $cliente->delete();
            
            return response()->json([
               "message" => "Cliente excluída" 
            ]);
        }
        else
        {
            return response()->json([
               "message" => "Cliente não encontrada" 
            ], 404);
        }
    }
    
    
    public function findByDocument($cpfcnpj)
    {
        if(Clientes::where('cpfcnpj', $cpfcnpj)->exists())
        {
            $cliente = Clientes::where('cpfcnpj', $cpfcnpj)->first();
            $entregas = Entregas::with('rastreamentos')->with('transportadoras')->where('id_destinatario', $cliente->id)->get();
            if(count($entregas) == 0)
            {
                return response()->json([
                    "message" => "Entregas para o CPF/CNPJ ".$cpfcnpj." não encontradas.",
                    "code" => 404,
                    "description" => $this->bydoc_description
                ]);
            }
            return response()->json([
                "message" => "Entregas para o CPF/CNPJ ".$cpfcnpj." encontradas.",
                "code" => 200,
                "description" => $this->bydoc_description,
                "data" => [
                    "cliente" => $cliente,
                    "entregas" => $entregas
                ]
            ]);
        }
        else
        {
            try 
            {
                $url = 'https://run.mocky.io/v3/6334edd3-ad56-427b-8f71-a3a395c5a0c7';
                $response = Http::get($url);
                
                if ($response->successful()) 
                {
                    // Retorna os dados da resposta
                    $dados = $response->json();
                    return $this->importFromApi($dados, $cpfcnpj);
                } 
                else 
                {
                    return response()->json([
                        "message" => "Erro ao buscar o cliente.",
                        "code" => $response->status(),
                        "description" => $this->bydoc_description
                    ], $response->status());
                }
            } 
            catch (\Throwable $th) 
            {
                return response()->json([
                    "message" => "Erro ao buscar o cliente.",
                    "error" => $th->getMessage(),
                    "code" => 404,
                    "description" => $this->bydoc_description
                ], 404);
            }
        }
    }
    
    protected function importFromApi($dados, $cpfcnpj)
    {
        if (!empty($dados)) 
        {
            if($dados['code'] == '200')
            {
                $data = $dados['data'];
                $cliente = false;
                foreach($data as $d)
                {
                    if($d['_destinatario']['_cpf'] == $cpfcnpj)
                    {
                        $cliente = $d;
                    }
                }
                
                if($cliente === false)
                {
                    return response()->json([
                        "message" => "Cliente não possui entregas",
                        "code" => 402,
                        "description" => "Cliente não possui entregas"
                    ], 401);
                }
                else
                {
                    $entregas = [];
                    foreach($data as $d)
                    {
                        if($d['_destinatario']['_cpf'] == $cpfcnpj)
                        {
                            $api_id_transportadora = $d['_id_transportadora'];
                            
                            if(Transportadoras::where('api_id', $api_id_transportadora)->exists())
                            {
                                $transportadora = Transportadoras::where('api_id', $api_id_transportadora)->first();
                                $id_transportadora = $transportadora->id;
                            }
                            else
                            {
                                try 
                                {
                                    $url = 'https://run.mocky.io/v3/e8032a9d-7c4b-4044-9d00-57733a2e2637';
                                    $response = Http::get($url);
                                    
                                    if ($response->successful()) 
                                    {
                                        $transportadora_dados = $response->json();
                                        
                                        if($transportadora_dados['code'] == '200')
                                        {
                                            foreach($transportadora_dados['data'] as $t)
                                            {
                                                $transportadora = new Transportadoras;
                                                $transportadora->api_id = $t['_id'];
                                                $transportadora->cnpj = $t['_cnpj'];
                                                $transportadora->fantasia = $t['_fantasia'];
                                                $transportadora->save();
                                                
                                                if($transportadora->api_id == $api_id_transportadora)
                                                {
                                                    $id_transportadora = $transportadora->id;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            return response()->json([
                                                "message" => "Erro ao buscar a transportadora da entrega: ".$transportadora_dados['message'],
                                                "code" => $transportadora_dados['code'],
                                                "description" => $this->bydoc_description
                                            ], $transportadora_dados['code']);
                                        }
                                        
                                    } 
                                    else 
                                    {
                                        return response()->json([
                                            "message" => "Erro ao buscar a transportadora da entrega.",
                                            "code" => $response->status(),
                                            "description" => $this->bydoc_description
                                        ], $response->status());
                                    }
                                } 
                                catch (\Throwable $th) 
                                {
                                    return response()->json([
                                        "message" => "Erro ao buscar a transportadora da entrega.",
                                        "error" => $th->getMessage(),
                                        "description" => $this->bydoc_description,
                                        "code" => 404
                                    ], 404);
                                }
                            }
                            
                            if(Clientes::where('cpfcnpj', $d['_destinatario']['_cpf'])->exists())
                            {
                                $cliente = Clientes::where('cpfcnpj', $d['_destinatario']['_cpf'])->first();
                            }
                            else
                            {
                                $cliente = new Clientes;
                                $cliente->cpfcnpj = $d['_destinatario']['_cpf'];
                                $cliente->nome = $d['_destinatario']['_nome'];
                                $cliente->endereco = $d['_destinatario']['_endereco'];
                                $cliente->estado = $d['_destinatario']['_estado'];
                                $cliente->cep = $d['_destinatario']['_cep'];
                                $cliente->pais = $d['_destinatario']['_pais'];
                                $cliente->geolocalizao = $d['_destinatario']['_geolocalizao']['_lat'].",".$d['_destinatario']['_geolocalizao']['_lng'];
                                $cliente->save();
                            }
                            
                            $id_cliente = $cliente->id;
                            
                            $entrega = new Entregas;
                            $entrega->id_transportadora = $id_transportadora;
                            $entrega->id_destinatario = $id_cliente;
                            $entrega->remetente = $d['_remetente']['_nome'];
                            $entrega->volumes = $d['_volumes'];
                            $entrega->api_id = $d['_id'];
                            $entrega->save();
                            
                            foreach($d['_rastreamento'] as $rastreio)
                            {
                                $rastreio['id_entrega'] = $entrega->id;
                                $rastreio['date'] = Carbon::parse($rastreio['date']);
                                $rastreio = Rastreamentos::create($rastreio);
                            }
                            
                            $entrega->rastreamentos = Rastreamentos::where('id_entrega', $entrega->id)->get();
                            $entrega->transportadora = Transportadoras::where('id', $entrega->id_transportadora)->first();
                            
                            $entregas[] = $entrega;
                        }
                    }
                }
                
                if(count($entregas) > 0)
                {
                    return response()->json([
                        "message" => "Entregas para o CPF/CNPJ ".$cpfcnpj." encontradas.",
                        "code" => 200,
                        "description" => $this->bydoc_description,
                        "data" => [
                            "cliente" => $cliente,
                            "entregas" => $entregas
                        ]
                    ], 200);
                }
                else
                {
                    return response()->json([
                        "message" => "Entregas para o CPF/CNPJ ".$cpfcnpj." não encontradas.",
                        "code" => 404,
                        "description" => $this->bydoc_description
                    ]);
                }
            }
            else
            {
                return response()->json([
                    "message" => "Erro ao buscar o cliente: ".$dados['message'],
                    "code" => $dados['code'],
                    "description" => $this->bydoc_description
                ], $dados['code']);
            }
        } 
        else 
        {
            // A decodificação falhou
            return response()->json([
                "message" => "Erro ao decodificar o JSON.",
                "code" => 404,
                "description" => $this->bydoc_description
            ], 404);
        }
    }
}