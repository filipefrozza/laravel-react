<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entregas extends Model
{
    use HasFactory;
    protected $table = 'entregas';
    protected $fillable = ['id_transportadora', 'volumes', 'id_remetente', 'id_destinatario'];
}
