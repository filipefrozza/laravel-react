<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;
    protected $table = 'clientes';
    protected $fillable = ['cpfcnpj', 'nome', 'endereco', 'estado', 'cep', 'pais', 'geolocalizao'];
}
