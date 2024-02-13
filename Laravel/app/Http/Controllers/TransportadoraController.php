<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transportadoras;

class TransportadoraController extends Controller
{
    public function index()
    {
        $transportadoras = Transportadoras::all();
        return response()->json($transportadoras);
    }
    
    public function store(Request $request)
    {
        $transportadora = new Transportadoras;
        $transportadora->cnpj = $request->cnpj;
        $transportadora->fantasia = $request->fantasia;
        $transportadora->save();
        return response()->json([
            "message" => "Transportadora cadastrada!"
        ], 201);
    }
    
    public function show($id)
    {
        $transportadora = Transportadoras::find($id);
        if(!empty($transportadora))
        {
            return response()->json($transportadora);
        }
        else
        {
            return response()->json([
                "message" => "Transportadora não encontrada"
            ], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        if(Transportadoras::where('id', $id)->exists())
        {
            $transportadora = Transportadoras::find($id);
            $transportadora->cnpj = is_null($request->cnpj) ? $transportadora->cnpj : $request->cnpj;
            $transportadora->fantasia = is_null($request->fantasia) ? $transportadora->fantasia : $request->fantasia;
            $transportadora->save();
            return response()->json([
                "message" => "Transportadora atualizada"
            ], 200);
        } 
        else 
        {
            return response()->json([
                "message" => "Transportadora não encontrada"
            ], 404);
        }
    }
    
    public function destroy($id)
    {
        if(Transportadoras::where('id', $id)->exists())
        {
            $transportadora = Transportadoras::find($id);
            $transportadora->delete();
            
            return response()->json([
               "message" => "Transportadora excluída" 
            ]);
        }
        else
        {
            return response()->json([
               "message" => "Transportadora não encontrada" 
            ], 404);
        }
    }
}
