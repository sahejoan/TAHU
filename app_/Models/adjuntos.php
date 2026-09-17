<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class adjuntos extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "adjuntos";
    protected $fillable=['nom_archivo','id_func',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];
}