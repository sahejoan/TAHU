<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class lcargos extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "lcargo";
    protected $fillable=['descripcion',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];
}