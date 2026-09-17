<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class transferirasistencia extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "transferidoasistencia";
    protected $fillable=['id','id_user', 'bloqueado',
                         'created_at', 'updated_at'
                        ];                       
}