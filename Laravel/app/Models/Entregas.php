<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rastreamentos;
use App\Models\Transportadoras;

class Entregas extends Model
{
    use HasFactory;
    protected $table = 'entregas';
    protected $fillable = ['id_transportadora', 'volumes', 'id_remetente', 'id_destinatario'];
    
    public function rastreamentos()
    {
        return $this->hasMany(Rastreamentos::class, 'id_entrega', 'id');
    }
    
    public function transportadoras()
    {
        return $this->belongsTo(Transportadoras::class, 'id_transportadora', 'id');
    }
}
