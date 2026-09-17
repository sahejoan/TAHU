<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
class calcularedad extends Model
{
	function edad($fecha_nac)
	{
    	$nacimiento = new DateTime($fecha_nac);
    	$ahora = new DateTime(date("Y-m-d"));
    	$diferencia = $ahora->diff($nacimiento);
    	return $diferencia->format("%y");
	}
}