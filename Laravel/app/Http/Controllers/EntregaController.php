<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entregas;
//['id_transportadora', 'volumes', 'id_remetente', 'id_destinatario'];

class EntregaController extends Controller
{
    public function index()
    {
        $entregas = Transportadoras::all();
        return response()->json($entregas);
    }
    
    public function store(Request $request)
    {
        $entrega = new Entregas;
        $entrega->id_transportadora = $request->id_transportadora;
        $entrega->volumes = $request->volumes;
        $entrega->id_remetente = $request->id_remetente;
        $entrega->id_destinatario = $request->id_destinatario;
        $entrega->save();
        return response()->json([
            "message" => "Entrega cadastrada!"
        ], 201);
    }
    
    public function show($id)
    {
        $entrega = Entregas::find($id);
        if(!empty($entrega))
        {
            return response()->json($entrega);
        }
        else
        {
            return response()->json([
                "message" => "Entrega não encontrada"
            ], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        if(Entregas::where('id', $id)->exists())
        {
            $entrega = Entregas::find($id);
            $entrega->id_transportadora = is_null($request->id_transportadora) ? $entrega->id_transportadora : $request->id_transportadora;
            $entrega->volumes = is_null($request->volumes) ? $entrega->volumes : $request->volumes;
            $entrega->id_remetente = is_null($request->id_remetente) ? $entrega->id_remetente : $request->id_remetente;
            $entrega->id_destinatario = is_null($request->id_destinatario) ? $entrega->id_destinatario : $request->id_destinatario;
            $entrega->save();
            return response()->json([
                "message" => "Entrega atualizada"
            ], 200);
        } 
        else 
        {
            return response()->json([
                "message" => "Entrega não encontrada"
            ], 404);
        }
    }
    
    public function destroy($id)
    {
        if(Entregas::where('id', $id)->exists())
        {
            $entrega = Entregas::find($id);
            $entrega->delete();
            
            return response()->json([
               "message" => "Entrega excluída" 
            ]);
        }
        else
        {
            return response()->json([
               "message" => "Entrega não encontrada" 
            ], 404);
        }
    }
}
