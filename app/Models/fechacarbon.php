<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Carbon\Carbon;

class fechacarbon extends Model
{
    public $ZonaHoraria = "";                   
    public $ZonaRef = "";                       

    public function iniciar() {
      $this->ZonaHoraria = env('APP_VALOR_ZONA_HORARIA');  //adelantar o atrasar zona horaria
      $this->ZonaRef = env('APP_VALOR_ZONA_REF');          //adelantar o atrasar zona horaria
    }

    public function fecha_a_str($fecha) {
      $this->iniciar();
      $result = new Carbon($fecha);

      if ($fecha != null) {
        if ($this->ZonaRef == "N") {
          $result  = $result->format('d-m-Y h:i:s a');
        }

        if ($this->ZonaRef == "R") {
          $result  =  $result->subMinute($this->ZonaHoraria);
          $result  =  $result->format('d-m-Y h:i:s a');
        }

        if ($this->ZonaRef == "S") {
          $result  = $result->addMinute($this->ZonaHoraria);
          $result  = $result->format('d-m-Y h:i:s a');
        }
      } else {
        $result = "";
      }

      return $result;
    }                            

    public function verfecha_a_str($fecha) {
      $this->iniciar();
      $result = new Carbon($fecha);

      if ($fecha != null) {
          $result  = $result->format('d-m-Y h:i:s a');
      } else {
        $result = "";
      }

      return $result;
    }  
    public function solofecha_a_str($fecha) {
      $this->iniciar();
      $result = new Carbon($fecha);

      if ($fecha != null) {
        if ($this->ZonaRef == "N") {
          //$result  = $result->format('d-m-Y h:i:s a');
          $result  = $result->format('d-m-Y');
        }

        if ($this->ZonaRef == "R") {
          $result  =  $result->subMinute($this->ZonaHoraria);
          //$result  =  $result->format('d-m-Y h:i:s a');
          $result  =  $result->format('d-m-Y');
        }

        if ($this->ZonaRef == "S") {
          $result  = $result->addMinute($this->ZonaHoraria);
          //$result  = $result->format('d-m-Y h:i:s a');
          $result  = $result->format('d-m-Y');
        }
      } else {
        $result = "";
      }

      return $result;
    }

    public function versolofecha_a_str($fecha) {
      $this->iniciar();
      $result = new Carbon($fecha);

      if ($fecha!=null) {
        $result  = $result->format('d-m-Y');
      } else {
        $result = "";
      }
      return $result;
    }

    public function fecha_tiempo_actual() {
      $this->iniciar();
      $result = new Carbon();

      if ($this->ZonaRef == "N") {
        $result = Carbon::now();
      }
      if ($this->ZonaRef == "R") {
        $result = Carbon::now()->subMinute($this->ZonaHoraria);
      } 
      if ($this->ZonaRef == "S") {
        $result = Carbon::now()->addMinute($this->ZonaHoraria);
      }

      return $result;
    }

    public function fecha_tiempo_actual_str() {
      $this->iniciar();
      $result = new Carbon();

      if ($this->ZonaRef == "N") {
        $result = Carbon::now();
      }
      if ($this->ZonaRef == "R") {
        $result = Carbon::now()->subMinute($this->ZonaHoraria);
      } 
      if ($this->ZonaRef == "S") {
        $result = Carbon::now()->addMinute($this->ZonaHoraria);        
      }
      
      return $result->format('d-m-Y g:i:s A');        
    }

    public function fecha_tiempo_actual_str24() {
      $this->iniciar();
      $result = new Carbon();

      if ($this->ZonaRef == "N") {
        $result = Carbon::now();
      }
      if ($this->ZonaRef == "R") {
        $result = Carbon::now()->subMinute($this->ZonaHoraria);
      } 
      if ($this->ZonaRef == "S") {
        $result = Carbon::now()->addMinute($this->ZonaHoraria);        
      }
      
      return $result->format('d-m-Y H:i:s');        
    }    

    public function solohora_a_str($fecha) {
      $this->iniciar();
      $result = new Carbon($fecha);

      if ($fecha!=null) {
        if ($this->ZonaRef == "N") {
          $result  = $result->format('h:i:s a');
        }

        if ($this->ZonaRef == "R") {
          $result  =  $result->subMinute($this->ZonaHoraria);
          $result  =  $result->format('h:i:s a');
        }

        if ($this->ZonaRef == "S") {
          $result  = $result->addMinute($this->ZonaHoraria);
          $result  = $result->format('h:i:s a');
        }
      } else {
        $result = "";
      }

      return $result;
    } 

    public function versolohora_a_str($fecha) {
      $this->iniciar();
      $result = new Carbon($fecha);

      if ($fecha!=null) {
          $result  = $result->format('h:i:s a');
      } else {
        $result = "";
      }

      return $result;
    }

    public function EntroAlas8($fecha,$size) {     
      if ($fecha!=null) {
        $horadeentrada = date("H:i",strtotime('2022-01-01 08:00:59'));
        $hr = date("H:i",strtotime($fecha));
        if($hr<=$horadeentrada) {
          return 'font-size: '.$size.'pt;';
        } else {
          return 'font-size: '.$size.'pt;color: #cb4335;';
        }
      } else {
        return 'font-size: '.$size.'pt;';
      }      
    }

    public function SalioAlas12($fecha,$size) {
      if ($fecha!=null) {
        $horadesalida = date("H:i",strtotime('2022-01-01 12:00:00'));
        $hr = date("H:i",strtotime($fecha));
        if($hr>=$horadesalida) {
          return 'font-size: '.$size.'pt;';
        } else {
          return 'font-size: '.$size.'pt;color: #cb4335;';
        }
      } else {
        return 'font-size: '.$size.'pt;';
      }      
    }

    public function EntroAlas1($fecha,$size) {     
      if ($fecha!=null) {
        $horadeentrada = date("H:i",strtotime('2022-01-01 13:00:59'));
        $hr = date("H:i",strtotime($fecha));
        if($hr<=$horadeentrada) {
          return 'font-size: '.$size.'pt;';
        } else {
          return 'font-size: '.$size.'pt;color: #cb4335;';
        }
      } else {
        return 'font-size: '.$size.'pt;';
      }      
    }

    public function SalioAlas4($fecha,$size) {
      if ($fecha!=null) {
        $horadesalida = date("H:i",strtotime('2022-01-01 16:00:00'));
        $hr = date("H:i",strtotime($fecha));
        if($hr>=$horadesalida) {
          return 'font-size: '.$size.'pt;';
        } else {
          return 'font-size: '.$size.'pt;color: #cb4335;';
        }
      } else {
        return 'font-size: '.$size.'pt;';
      }      
    }

    public function EoS($fecha1,$fecha2,$fecha3,$fecha4,$fecha5,$fecha6,$fecha7,$fecha8,$fecha9,$fecha10,$fecha11,$fecha12,$fecha13,$fecha14,$fecha15,$fecha16,$indice,$horaactual,$size) {

      $mf = [
        '0'=>$fecha1,
        '1'=>$fecha2,
        '2'=>$fecha3,
        '3'=>$fecha4,
        '4'=>$fecha5,
        '5'=>$fecha6,
        '6'=>$fecha7,
        '7'=>$fecha8,
        '8'=>$fecha9,
        '9'=>$fecha10,
        '10'=>$fecha11,
        '11'=>$fecha12,
        '12'=>$fecha13,
        '13'=>$fecha14,
        '14'=>$fecha15,
        '15'=>$fecha16       
      ];
      $c = 0;
      for ($i=0;$i<16;$i++) {
        if ($mf[$i]!=null) $c++;
      }

      $funcion = [];      
      if ($c>1) {           
        if ($fecha1!=null) {                          
          $f1 = date("Y/m/d",strtotime($fecha1));
          $fa = date("Y/m/d",strtotime($horaactual));
          $ha = date("H:i",strtotime($horaactual));
          $hc = date("H:i",strtotime('2022-01-01 16:00:00'));

          if ($f1 == $fa) {    //consultado el mismo dia            
            if ($ha < $hc) {   //consulta es antes de las 4              
              if ($c == 2) {
                $funcion = ['0'=>'','1'=>'SalioAlas12','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
              }
              if ($c == 3) {
                $funcion = ['0'=>'','1'=>'SalioAlas12','2'=>'EntroAlas1','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
              }            
              if ($c == 4) {
                $funcion = ['0'=>'','1'=>'SalioAlas12','2'=>'EntroAlas1','3'=>'SalioAlas4','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
              }                        
              if ($c > 4) {
                $funcion = ['0'=>'','1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
                $funcion[$c-1] = 'SalioAlas4';
              }                                    
            } else {          //consulta es despues de las 4                    
              if ($c == 2) {
                $funcion = ['0'=>'','1'=>'SalioAlas4','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
              }
              if ($c == 3) {
                $funcion = ['0'=>'','1'=>'SalioAlas12','2'=>'EntroAlas1','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
              }            
              if ($c == 4) {
                $funcion = ['0'=>'','1'=>'SalioAlas12','2'=>'EntroAlas1','3'=>'SalioAlas4','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
              }                        
              if ($c > 4) {
                $funcion = ['0'=>'','1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
                $funcion[$c-1] = 'SalioAlas4';
              }                                    
            }
          } else {            //consultado en otra fecha                          
            if ($c == 2) {
              $funcion = ['0'=>'','1'=>'SalioAlas4','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
            }
            if ($c == 3) {
              $funcion = ['0'=>'','1'=>'SalioAlas12','2'=>'EntroAlas1','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
            }            
            if ($c == 4) {
              $funcion = ['0'=>'','1'=>'SalioAlas12','2'=>'EntroAlas1','3'=>'SalioAlas4','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
            }                        
            if ($c > 4) {
              $funcion = ['0'=>'','1'=>'','2'=>'','3'=>'','4'=>'','5'=>'','6'=>'','7'=>'','8'=>'','9'=>'','10'=>'','11'=>'','12'=>'','13'=>'','14'=>'','15'=>''];
              $funcion[$c-1] = 'SalioAlas4';
            }                                    
          }

          if ($funcion[$indice]=='SalioAlas12') {
            return $this->SalioAlas12($mf[$indice],$size);
          } else {
            if ($funcion[$indice]=='EntroAlas1'){
              return $this->EntroAlas1($mf[$indice],$size);
            } else {
              if ($funcion[$indice]=='SalioAlas4'){
                return $this->SalioAlas4($mf[$indice],$size);
              } else {
                return 'font-size: '.$size.'pt;';
              }              
            }            
          }

        } else {
          return 'font-size: '.$size.'pt;';
        }
      } else {
        return 'font-size: '.$size.'pt;';
      }
    }    
}