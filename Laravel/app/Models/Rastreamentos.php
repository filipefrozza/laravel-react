<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rastreamentos extends Model
{
    use HasFactory;
    protected $table = 'rastreamentos';
    protected $fillable = ['id_entrega', 'message', 'date'];
}
