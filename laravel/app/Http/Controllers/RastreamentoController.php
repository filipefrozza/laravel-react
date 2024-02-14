<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rastreamentos;

class RastreamentoController extends Controller
{
    public function index()
    {
        $rastreamentos = Rastreamentos::all();
        return response()->json($rastreamentos);
    }
    
    public function store(Request $request)
    {
        $rastreamento = new Rastreamentos;
        $rastreamento->id_entrega = $request->id_entrega;
        $rastreamento->message = $request->message;
        $rastreamento->date = $request->date;
        $rastreamento->save();
        return response()->json([
            "message" => "Rastreamento cadastrado!"
        ], 201);
    }
    
    public function show($id)
    {
        $rastreamento = Rastreamentos::find($id);
        if(!empty($rastreamento))
        {
            return response()->json($rastreamento);
        }
        else
        {
            return response()->json([
                "message" => "Rastreamento não encontrado"
            ], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        if(Rastreamentos::where('id', $id)->exists())
        {
            $rastreamento = Rastreamentos::find($id);
            $rastreamento->id_entrega = is_null($request->id_entrega) ? $rastreamento->id_entrega : $request->id_entrega;
            $rastreamento->message = is_null($request->message) ? $rastreamento->message : $request->message;
            $rastreamento->date = is_null($request->date) ? $rastreamento->date : $request->date;
            $rastreamento->save();
            return response()->json([
                "message" => "Rastreamento atualizado"
            ], 200);
        } 
        else 
        {
            return response()->json([
                "message" => "Rastreamento não encontrado"
            ], 404);
        }
    }
    
    public function destroy($id)
    {
        if(Rastreamentos::where('id', $id)->exists())
        {
            $rastreamento = Rastreamentos::find($id);
            $rastreamento->delete();
            
            return response()->json([
               "message" => "Rastreamento excluído" 
            ]);
        }
        else
        {
            return response()->json([
               "message" => "Rastreamento não encontrado"
            ], 404);
        }
    }
}
