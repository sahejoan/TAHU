<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tipificaciones extends Model
{

    public $condiciones = [
        ""       			        => "Seleccionar", 
        "Contratado"                => "Contratado", 
        "Pasante"        	        => "Pasante", 
        "Fijo"        		        => "Fijo", 
        "Jefe de Division"          => "Jefe de Division",
        "Jefe de Sector"            => "Jefe de Sector",
        "Jefe de Unidad"            => "Jefe de Unidad",
        "Auditor 99"                => "Auditor 99",
        "Servicios Medicos"          => "Servicios Medicos",
        "Tecn. Administrativo"       => "Tecn. Administrativo",
        "Prof. Administrativo"       => "Prof. Administrativo",            
        "Auditor Aduanero y Trib."   => "Auditor Aduanero y Trib.",
        "Asist. Administrativo"      => "Asist. Administrativo",
        "Prof. Aduanero y Tributario"=> "Prof. Aduanero y Tributario",
        "Tecn. Aduanero y Tributario"=> "Tecn. Aduanero y Tributario",
        "Obrero"                     => "Obrero",
        "Gerente"                    => "Gerente",
        "Supervisor Regional ONIPC"  => "Supervisor Regional ONIPC",
        "Oficial de Seguridad"       => "Oficial de Seguridad",
    ];
            

    public $estatuscontratado = [
                ""       		=> "Seleccionar", 
                "Activo"        => "Activo", 
                "Vencido"       => "Vencido", 
                "Renovación"    => "Renovación", 
                "Culminado"		=> "Culminado"
    ];

    public $estatusdesignacion = [
                ""              => "Seleccionar", 
                "Activo"        => "Activo", 
                "Culminado"     => "Culminado",
                "Jubilado"      => "Jubilado"                
    ];

    public $estatuscargo = [
                ""       			=> "Seleccionar", 
                "Activo"        	=> "Activo", 
                "Cese en la función"=> "Cese en la función", 
                //"Sin designación"   => "Sin designación"
    ];

    public $sexo_persona = [
                            "M"   => "M",
                            "F"   => "F"
                           ];

    public $parentesco =   [
                            ""           => "Seleccionar",
                            "Hijo"       => "Hijo",
                            "Hija"       => "Hija",
                            "Padre"      => "Padre",
                            "Madre"      => "Madre",
                            "Conyugue"   => "Conyugue",
                           ];

    public $jefes = [
        "" => "Seleccionar",
        "Gerencia Regional Los LLanos" => "Gerencia Regional Los LLanos",
        "S.T.I. San Fernando de Apure" => "S.T.I. San Fernando de Apure",
        "S.T.I. Altagracia de Orituco" => "S.T.I. Altagracia de Orituco",
        "S.T.I. San Juan de los Morros" => "S.T.I. San Juan de los Morros",
        "S.T.I. Valle de la Pascua" => "S.T.I. Valle de la Pascua",
    ];
}

?>